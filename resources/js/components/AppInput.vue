<template>
    <div class="border rounded">
        <template v-if="icon">
        </template>
        <input :placeholder="placeholder" type="text" @input="onChange">
    </div>
    
</template>

<script>
export default {
    name: "AppInput",
    props: {
        modelValue: {
            type: null
        },
        icon: {
            type: String,
            default: null
        },
        placeholder: {
            type: String,
            default: null,
        }
    },
    methods: {
        onChange() {
            this.$emit("update:modelValue", $event.target.value)
        }
    }
}
</script>