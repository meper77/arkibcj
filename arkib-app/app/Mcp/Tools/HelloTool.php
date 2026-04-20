<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Greet a user by name.')]
class HelloTool extends Tool
{
    public function handle(Request $request): Response
    {
        $name = $request->input('name', 'World');

        return Response::text("Hello, {$name}! Welcome to Arkib.");
    }

    /**
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string('The name to greet.'),
        ];
    }
}
