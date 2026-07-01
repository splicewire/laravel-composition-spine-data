<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Keyword prefix
    |--------------------------------------------------------------------------
    |
    | The namespace applied to this engine's private x-* schema keywords
    | (x-{prefix}-beat, x-{prefix}-generate, …), keeping them out of the shared
    | OpenAPI x- commons. Base/standard vocabulary (@id, x-dereference) is NOT
    | prefixed. Each engine owns its own prefix (composition: swc, knowledge: swk).
    |
    */
    'keyword_prefix' => env('COMPOSITION_KEYWORD_PREFIX', 'swc'),
];
