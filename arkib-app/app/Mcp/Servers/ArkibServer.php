<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\HelloTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Arkib Server')]
#[Version('0.0.1')]
#[Instructions('MCP server for the Arkib Laravel application.')]
class ArkibServer extends Server
{
    protected array $tools = [
        HelloTool::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}
