<script>
export default {
    name: 'Persistent',
    created() {
        if (!this.$root._persistentComponents) {
            this.$root._persistentComponents = {};
        }
    },
    mounted() {
        // Move the component's content to a persistent container
        if (!document.getElementById('persistent-components')) {
            const container = document.createElement('div');
            container.id = 'persistent-components';
            document.body.appendChild(container);
        }

        const placeholder = document.createComment('persistent-placeholder');
        this.$el.parentNode.insertBefore(placeholder, this.$el);
        document.getElementById('persistent-components').appendChild(this.$el);
        this.$root._persistentComponents[this._uid] = {
            placeholder,
            element: this.$el,
        };
    },
    beforeUnmount() {
        // Restore the component's content to its original location
        const { placeholder, element } = this.$root._persistentComponents[this._uid];
        placeholder.parentNode.insertBefore(element, placeholder);
        placeholder.remove();
        delete this.$root._persistentComponents[this._uid];
    },
};
</script>

<template>
    <div class="persistent-component">
        <slot />
    </div>
</template>
