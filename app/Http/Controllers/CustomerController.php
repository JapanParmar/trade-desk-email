<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\GmailSmtpService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    protected GmailSmtpService $smtpService;

    public function __construct(GmailSmtpService $smtpService)
    {
        $this->smtpService = $smtpService;
    }

    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('gmail', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('broker_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('status', $request->status);
        }

        $customers = $query->withCount('emailLogs')->latest()->paginate(10)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'customers' => $customers,
            ]);
        }

        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gmail' => 'required|email|unique:customers,gmail',
            'app_password' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'broker_code' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        $customer = Customer::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully!',
                'customer' => $customer,
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Customer added successfully!');
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gmail' => ['required', 'email', Rule::unique('customers')->ignore($customer->id)],
            'app_password' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'broker_code' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        $customer->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer details updated successfully!',
                'customer' => $customer,
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully!');
    }

    public function destroy(Customer $customer, Request $request)
    {
        $customer->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully!',
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully!');
    }

    public function toggleStatus(Customer $customer)
    {
        $customer->status = $customer->status === 'active' ? 'inactive' : 'active';
        $customer->save();

        return response()->json([
            'success' => true,
            'status' => $customer->status,
            'message' => "Customer status changed to {$customer->status}.",
        ]);
    }

    public function testSmtp(Customer $customer)
    {
        $result = $this->smtpService->testConnection($customer);
        return response()->json($result);
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $file = fopen($path, 'r');
        $header = fgetcsv($file);

        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) < 3) continue;

            $name = trim($row[0] ?? '');
            $gmail = trim($row[1] ?? '');
            $appPassword = trim($row[2] ?? '');
            $mobile = trim($row[3] ?? '');
            $brokerCode = trim($row[4] ?? '');

            if (empty($gmail) || empty($appPassword)) {
                $skipped++;
                continue;
            }

            Customer::updateOrCreate(
                ['gmail' => $gmail],
                [
                    'name' => $name ?: explode('@', $gmail)[0],
                    'app_password' => $appPassword,
                    'mobile' => $mobile ?: 'N/A',
                    'broker_code' => $brokerCode ?: null,
                    'status' => 'active',
                ]
            );

            $imported++;
        }

        fclose($file);

        return redirect()->route('customers.index')->with('success', "Imported {$imported} customers successfully! ({$skipped} skipped)");
    }

    public function downloadSampleCsv()
    {
        $csvHeader = ['Name', 'Gmail', 'AppPassword', 'Mobile', 'BrokerCode'];
        $sampleData = [
            ['Rajesh Sharma', 'rajesh.sharma@gmail.com', 'abcd efgh ijkl mnop', '+91 98765 43210', 'BRK-1001'],
            ['Priya Patel', 'priya.patel@gmail.com', 'wxyz qwer tyui opas', '+91 91234 56789', 'BRK-1002'],
        ];

        $callback = function () use ($csvHeader, $sampleData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sample_clients_import.csv"',
        ]);
    }
}
