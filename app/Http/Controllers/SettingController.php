<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $brokerageEmail = Setting::get('brokerage_email', 'orders@capitalbrokerage.com');
        $companyName = Setting::get('company_name', 'Capital Vantage Brokerage');

        return view('settings.index', compact('brokerageEmail', 'companyName'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'brokerage_email' => 'required|email',
            'company_name' => 'required|string|max:255',
        ]);

        Setting::set('brokerage_email', $request->brokerage_email);
        Setting::set('company_name', $request->company_name);

        return redirect()->route('settings.index')->with('success', 'System settings saved successfully!');
    }
}
