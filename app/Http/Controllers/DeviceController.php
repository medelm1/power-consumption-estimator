<?php

namespace App\Http\Controllers;

use App\DTOs\DeviceDTO;
use App\Http\Responses\DeviceJsonResponse;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DeviceController extends Controller
{
    private DeviceService $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    public function index() 
    {
        $devices = $this->deviceService->getAllDevices();
        return new DeviceJsonResponse($devices);
    }

    public function show($id) 
    {
        $device = $this->deviceService->getDeviceById($id);

        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        return new DeviceJsonResponse($device);     
    }

    public function store(Request $request) 
    {
        try {
            $deviceDTO = new DeviceDTO($request->all());
            $newCreatedDevice = $this->deviceService->createDevice($deviceDTO);

            return new DeviceJsonResponse($newCreatedDevice, 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => $e->validator->errors()], 442);
        }
    }

    public function update(Request $request, $id) 
    {
        $deviceDTO = new DeviceDTO($request->all());
        $updatedDevice = $this->deviceService->updateDevice($id, $deviceDTO);

        if (!$updatedDevice) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        return new DeviceJsonResponse($updatedDevice, 200);
    }

    public function destroy($id) 
    {
        $deleted = $this->deviceService->deleteDevice($id);
        
        if (!$deleted) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        return response()->json(null, 204);
    }
}
