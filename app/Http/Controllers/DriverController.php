<?php

namespace App\Http\Controllers;

use App\Models\Driver;

class DriverController extends Controller
{
    public function createDriver($name)
    {
        $driver = new Driver();
        $driver->name = $name;
        $driver->save();
        return $driver;
    }

    public function getDriver($name)
    {
        $driver = Driver::where('name', $name)->first();
        if ($driver) {
            return $driver;
        }
        return null;
    }

    public function updateDriver($name, $newName)
    {
        $driver = Driver::where('name', $name)->first();
        if ($driver) {
            $driver->name = $newName;
            $driver->save();
            return $driver;
        }
        return null;
    }

    public function deleteDriver($name)
    {
        $driver = Driver::where('name', $name)->first();
        if ($driver) {
            $driver->delete();
            return true;
        }
        return false;
    }

    public function getAllDrivers()
    {
        return Driver::all();
    }

    public function checkDriver($name)
    {
        $driver = Driver::where('name', $name)->first();
        if ($driver) {
            return $driver;
        }
        return null;
    }
}
