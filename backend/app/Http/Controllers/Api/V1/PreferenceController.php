<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Filters\Api\V1\Filters\PreferenceFilter;
use App\Http\Requests\PreferenceRequest;
use App\Http\Resources\Api\V1\PreferenceResource;
use App\Services\V1\PreferenceServices;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PreferenceController extends ApiController
{
    public function __construct(protected PreferenceServices $preferenceServices) {}

    /**
     * Get System's default Preferences object.
     *
     * Returns single record.
     * To be replaced when we have authentication and user-specific preferences.
     */
    public function index(PreferenceFilter $filters)
    {
        try {
            $preference = $this->preferenceServices->getPreferences($filters);

            return $this->ok('Preferences Retrieved Successfully', new PreferenceResource($preference));
        } catch (\Exception $e) {
            return $this->error('Preferences Retrieval Error', null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PreferenceRequest $request)
    {
        try {
            $preference = $this->preferenceServices->createPreference($request->validated());

            return $this->ok('Preferences Created', new PreferenceResource($preference), 201);
        } catch (\Exception $e) {

            return $this->error('Preferences Creation Error', null, 500, $e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PreferenceRequest $request, string $id)
    {
        try {
            $preference = $this->preferenceServices->getPreferenceById($id);
            $preference = $this->preferenceServices->updatePreference($preference, $request->validated());

            return $this->ok('Preference Updated', new PreferenceResource($preference));
        } catch (ModelNotFoundException $e) {
            return $this->error('Preference not found', null, 404);
        } catch (\Exception $e) {

            return $this->error('Preference Update Error', null, 500, $e);
        }
    }

    /**
     * Display the specified resource.
     *
     * TODO:: Implement when we have authentication. Same with delete.
     */

    //    public function show(Preference $preference)
    //    {
    //        //
    //    }
}
