<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiCommand;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ApiCommandController extends Controller {
    public function executeCommand(Request $request, ApiCommand $command) {
        $user = $request->user();
        $tokenPermissions = $user->tokens->first()->abilities ?? [];

        $requiredPermissions = $command->permissions->pluck('permission_name')->toArray();

        if (count(array_intersect($requiredPermissions, $tokenPermissions)) !== count($requiredPermissions)) {
            return response()->json(['error' => 'Unauthorized - Missing required permissions'], 403);
        }

        $process = new Process(explode(' ', $command->command));
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return response()->json([
            'output' => $process->getOutput(),
        ]);
    }
}
