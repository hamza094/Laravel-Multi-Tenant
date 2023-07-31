<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;


class TenantController extends Controller
{
    public function changeTenant($tenantID)
    {
        // Check tenant
        $tenant = auth()->user()->tenants()->findOrFail($tenantID);

        // Change tenant
        auth()->user()->update(['current_tenant_id' => $tenantID]);

        $tenantDomain = str_replace('://', '://' . $tenant->subdomain . '.', config('app.url'));
        
        return redirect($tenantDomain . RouteServiceProvider::HOME);
    }
}
