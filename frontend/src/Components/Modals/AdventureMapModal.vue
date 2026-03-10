<script setup>
import { VueFinalModal } from 'vue-final-modal';
import { $t } from '@/i18n.js';
import { VNetworkGraph } from 'v-network-graph';
import { computed, reactive, ref } from 'vue';
import dagre from '@dagrejs/dagre';

const emit = defineEmits(['close']);

const props = defineProps({
    adventure: {
        type: Object,
        required: true,
    },
});

const graph = ref(null);

const layouts = reactive({
    nodes: {},
});

const nodeSize = 40;

const configs = vNG.defineConfigs({
    view: {
        autoPanAndZoomOnLoad: 'fit-content',
        onBeforeInitialDisplay: () => layout('TB'),
    },
    node: {
        normal: {
            radius: nodeSize / 2,
            color: '#E836C5',
        },
        hover: {
            radius: nodeSize / 2,
            color: '#921178',
        },
        label: {
            visible: true,
            fontFamily: 'Merge One, sans-serif',
            fontSize: 16,
            lineHeight: 1.1,
            color: '#5a5c69',
            margin: 4,
            direction: 'north',
            background: {
                visible: false,
                color: '#ffffff',
                padding: {
                    vertical: 1,
                    horizontal: 4,
                },
                borderRadius: 2,
            },
        },
    },
    edge: {
        selectable: false,
        normal: {
            color: '#868686',
            width: 3,
        },
        hover: {
            color: '#5a5c69',
            width: 4,
            animate: false,
        },
        margin: 4,
        marker: {
            target: {
                type: 'arrow',
                width: 4,
                height: 4,
            },
        },
    },
});

const nodes = computed(() => {
    const nodes = {};
    let counter = 1;

    nodes[getNodeName(counter++)] = {
        name: props.adventure.title,
        type: $t('modals.adventure.heading'),
    };

    for (const root of props.adventure.children) {
        let rootIndex = 1;

        nodes[getNodeName(counter++)] = {
            name: root.title,
            type: $t('modals.adventure.root', { index: rootIndex++ }),
        };

        for (const final of root.children) {
            const finalIndex = 1;

            nodes[getNodeName(counter++)] = {
                name: final.title,
                type: $t('modals.adventure.final', { index: finalIndex }),
            };
        }
    }

    return nodes;
});

const edges = computed(() => {
    const edges = {};
    let edgeCounter = 1;

    // This counter must be an exact copy of the one used in the 'nodes' method
    let nodeCounter = 1;

    // Skip the first node (heading) since it's the start point.
    // We'll connect the roots to it.
    const headingNodeName = getNodeName(nodeCounter++);

    for (const root of props.adventure.children) {
        const rootNodeName = getNodeName(nodeCounter);

        // Create an edge from the main heading to the current root node
        edges[getEdgeName(edgeCounter++)] = {
            source: headingNodeName,
            target: rootNodeName,
        };

        // Increment the node counter to move past the current root
        nodeCounter++;

        // Loop through the finals of the current root
        for (const final of root.children) {
            const finalNodeName = getNodeName(nodeCounter);

            // Create an edge from the current root to its final node
            edges[getEdgeName(edgeCounter++)] = {
                source: rootNodeName,
                target: finalNodeName,
            };

            // Increment the node counter to move to the next final
            nodeCounter++;
        }
    }

    return edges;
});

const getNodeName = (counter) => {
    return `node${counter}`;
};

const getEdgeName = (counter) => {
    return `edge${counter}`;
};

const layout = (direction = 'LR') => {
    if (Object.keys(nodes).length <= 1 || Object.keys(edges).length === 0) {
        return;
    }

    // convert graph
    // ref: https://github.com/dagrejs/dagre/wiki
    const g = new dagre.graphlib.Graph();
    // Set an object for the graph label
    g.setGraph({
        rankdir: direction,
        nodesep: nodeSize * 2,
        edgesep: nodeSize,
        ranksep: nodeSize,
    });
    // Default to assigning a new object as a label for each new edge.
    g.setDefaultEdgeLabel(() => ({}));

    // Add nodes to the graph. The first argument is the node id. The second is
    // metadata about the node. In this case we're going to add labels to each of
    // our nodes.
    Object.entries(nodes.value).forEach(([nodeId, node]) => {
        g.setNode(nodeId, { label: node.name, width: nodeSize, height: nodeSize });
    });

    // Add edges to the graph.
    Object.values(edges.value).forEach((edge) => {
        g.setEdge(edge.source, edge.target);
    });

    dagre.layout(g);

    g.nodes().forEach((nodeId) => {
        // update node position
        const x = g.node(nodeId).x;
        const y = g.node(nodeId).y;
        layouts.nodes[nodeId] = { x, y };
    });
};
</script>

<template>
    <VueFinalModal
        class="d-flex justify-content-center align-items-center"
        content-class="bg-default position-absolute top-50 start-50 translate-middle rounded-4 d-flex flex-column p-4 h-50 w-75 max-height-75"
        content-transition="vfm-fade"
        overlay-transition="vfm-fade"
        modal-id="adventure-map-modal"
    >
        <div class="container-fluid d-flex flex-column w-100 h-100">
            <div class="w-100 text-center font-default fs-5 mb-2">
                {{ $t('modals.adventure.title') }}
            </div>
            <VNetworkGraph
                ref="graph"
                class="graph"
                :nodes="nodes"
                :edges="edges"
                :layouts="layouts"
                :configs="configs"
            />
        </div>
    </VueFinalModal>
</template>
