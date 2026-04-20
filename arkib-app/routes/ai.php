<?php

use App\Mcp\Servers\ArkibServer;
use Laravel\Mcp\Facades\Mcp;

// Local stdio transport (for Claude Code CLI)
Mcp::local('arkib', ArkibServer::class);

// Web HTTP transport (for remote MCP clients with OAuth)
Mcp::web('/mcp', ArkibServer::class)
    ->middleware('auth:api');

// OAuth discovery & registration endpoints
Mcp::oauthRoutes();
