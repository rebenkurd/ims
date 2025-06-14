<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyProfileRequest;
use App\Http\Resources\CompanyProfileResource;
use App\Models\CompanyProfile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use App\Trait\SaveImageTrait;

class CompanyProfileController extends Controller
{
    use SaveImageTrait;

    /**
     * Display the company profile.
     */
    public function show()
    {
        $companyProfile = CompanyProfile::first();

        if (!$companyProfile) {
            return response()->json([
                'message' => 'Company profile not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Company profile retrieved successfully',
            'data' => new CompanyProfileResource($companyProfile)
        ]);
    }

    /**
     * Update the company profile in storage.
     */
  /**
 * Update the company profile in storage.
 */
public function update(CompanyProfileRequest $request)
{
    try {
        $data = $request->validated();

        // Handle file upload
        if ($request->hasFile('logo')) {
            $relativePath = $this->saveImage($request->file('logo'), 'company');
            $data['logo'] = URL::to('/storage/'.$relativePath);

            // Delete old logo if exists
            $existing = CompanyProfile::first();
            if ($existing && $existing->logo) {
                $oldImagePath = str_replace(URL::to('/storage/'), '', $existing->logo);
                Storage::disk('public')->delete($oldImagePath);
            }
        } else {
            // Keep existing logo path if no new file was uploaded
            $existing = CompanyProfile::first();
            if ($existing && $existing->logo) {
                $data['logo'] = $existing->logo;
            }
        }

        // Update or create the company profile
        $companyProfile = CompanyProfile::updateOrCreate(
            ['id' => 1],
            $data
        );

        return response([
            "message" => "Company profile updated successfully",
            "data" => new CompanyProfileResource($companyProfile)
        ]);

    } catch (\Exception $e) {
        return response([
            "message" => "Error updating company profile: ". $e->getMessage()
        ], 500);
    }
}


}
