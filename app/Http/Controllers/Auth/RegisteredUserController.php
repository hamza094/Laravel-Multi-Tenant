<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use App\Models\Invitation;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
         $invitationEmail = NULL;
        if (request('token')) {
            $invitation = Invitation::where('token', request('token'))
                ->whereNull('accepted_at')
                ->firstOrFail();

            $invitationEmail = $invitation->email;
        }

        return view('auth.register', compact('invitationEmail'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'subdomain' => ['sometimes', 'alpha', 
              'unique:tenants,subdomain'],
        ]);

        $email = $request->email;
         
         if ($request->token) {
            $invitation = Invitation::with('tenant')
                ->where('token', $request->token)
                ->whereNull('accepted_at')
                ->first();

            if (!$invitation) {
                return redirect()->back()->withInput()->withErrors(['email' => __('Invitation link incorrect')]);
            }

            $email = $invitation->email;
        } 

        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($request->password),
        ]);

        $subdomain = $request->subdomain;

        if($invitation){
            $invitation->update(['accepted_at' => now()]);

            $invitation->tenant->users()->attach($user->id);

            $user->update(['current_tenant_id' => $invitation->tenant_id]);

            $subdomain = $invitation->tenant->subdomain;

        }else{

        $tenant = Tenant::create([
            'name' => $request->name . ' Team',
            'subdomain' => $request->subdomain
        ]);

        $tenant->users()->attach($user->id, ['is_owner' => true]);
        
        $user->update(['current_tenant_id' => $tenant->id]);

        }


        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);


    //$tenantDomain = 'http://' . $subdomain . '.multi.test';

    //return redirect($tenantDomain . RouteServiceProvider::HOME);

    }
}
