<?php
declare(strict_types=1);

namespace phpDocumentor\GraphViz;

enum GraphType: string
{
    case DIGRAPH = 'digraph';
    case GRAPH = 'graph';
    case SUBGRAPH = 'subgraph';
}
