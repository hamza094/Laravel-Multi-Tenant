<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function changeTenant($tenantID)
    {
        // Check tenant
        $tenant = auth()->user()->tenants()->findOrFail($tenantID);

        // Change tenant
        auth()->user()->update(['current_tenant_id' => $tenantID]);

        // Redirect to dashboard
        return redirect()->route('dashboard');
    }
}
