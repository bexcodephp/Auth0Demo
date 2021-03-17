<?php

namespace App\Http\Controllers;

// use App\Http\Requests\API\CreateCloudVendorSettingsAPIRequest;
// use App\Http\Requests\API\UpdateCloudVendorSettingsAPIRequest;
use App\Models\Users;
use App\Repositories\CustomUserRepository;
//use App\Http\Controllers\AppBaseController;
//use App\Http\Resources\CloudVendorSettingsResource;
use Response;

use Illuminate\Http\Request;

class UserController extends Controller
{
    private $cloudVendorSettingsRepository;

    public function __construct(CustomUserRepository $CustomUsersRepo)
    {
        $this->cloudVendorSettingsRepository = $cloudVendorSettingsRepo;
    }

    public function store(CreateUsersRequest $request)
    {
        $input = $request->all();

        $CustomUsers = $this->CustomUserRepository->create($input);

        return $this->sendResponse(new UsersResource($CustomUsers), 'User saved successfully');
    }
}
