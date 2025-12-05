<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SystemSettingController extends Controller {
    /**
     * Display the system settings page.
     *
     * @return View
     */
    public function index(): View {
        $setting = SystemSetting::latest('id')->first();
        return view('backend.layouts.settings.system_settings', compact('setting'));
    }

    /**
     * Update the system settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse {
        $validator = Validator::make($request->all(), [
            'title'          => 'nullable',
            'email'          => 'nullable',
            'system_name'    => 'nullable',
            'copyright_text' => 'nullable',
            'logo'           => 'nullable',
            'favicon'        => 'nullable',
            'description'    => 'nullable',
            'facebook'    => 'nullable',
            'twitter'    => 'nullable',
            'linkedin'    => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            $setting                 = SystemSetting::firstOrNew();
            $setting->title          = $request->title;
            $setting->email          = $request->email;
            $setting->system_name    = $request->system_name;
            $setting->copyright_text = $request->copyright_text;
            $setting->logo           = $request->logo;
            $setting->favicon        = $request->favicon;
            $setting->description    = $request->description;
            $setting->facebook       = $request->facebook;
            $setting->twitter        = $request->twitter;
            $setting->linkedin       = $request->linkedin;

            if ($request->hasFile('logo')) {
                // Handle logo upload
                $logoPath = null;
                if ($request->hasFile('logo')) {
                    // Delete old logo if it exists
                    if ($setting->logo && Storage::disk('public')->exists($setting->logo)) {
                        Storage::disk('public')->delete($setting->logo);
                    }

                    $logo = $request->file('logo');
                    $logoName = time() . '_' . $logo->getClientOriginalName();
                    $logoPath = $logo->storeAs('uploads/logo', $logoName, 'public');
                    $setting->logo = $logoPath;
                }

            } else {
                // Retain the existing logo if no new file is uploaded
                $setting->logo = $setting->logo ?? $setting->getOriginal('logo');
            }

            if ($request->hasFile('favicon')) {

                // Handle favicon upload
                $faviconPath = null;
                if ($request->hasFile('favicon')) {
                    // Delete old favicon if it exists
                    if ($setting->favicon && Storage::disk('public')->exists($setting->favicon)) {
                        Storage::disk('public')->delete($setting->favicon);
                    }

                    $favicon = $request->file('favicon');
                    $faviconName = time() . '_' . $favicon->getClientOriginalName();
                    $faviconPath = $favicon->storeAs('uploads/favicon', $faviconName, 'public');
                    $setting->favicon = $faviconPath;
                }

            } else {
                // Retain the existing favicon if no new file is uploaded
                $setting->favicon = $setting->favicon ?? $setting->getOriginal('favicon');
            }

            $setting->save();
            return back()->with('t-success', 'Updated successfully');
        } catch (Exception) {
            return back()->with('t-error', 'Failed to update');
        }
    }
}
