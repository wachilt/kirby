<template>
	<k-frame
		v-bind="$props"
		:style="{ '--color-frame-back': color }"
		class="k-color-frame"
	>
		<slot />
	</k-frame>
</template>

<script>
import { props as FrameProps } from "./Frame.vue";

export const props = {
	mixins: [FrameProps],
	props: {
		color: String
	}
};

/**
 * Use <k-color-frame> to display a color preview.
 * @since 4.0.0
 *
 * @example <k-color-frame color="#efefef" ratio="1/1" />
 */
export default {
	mixins: [props],
	inheritAttrs: false
};
</script>

<style>
:root {
	--color-frame-back: none;
	--color-frame-rounded: var(--rounded);
	--color-frame-size: 100%;
	--color-frame-darkness: 0%;
}
.k-color-frame.k-frame {
	background: var(--pattern-light);
	width: var(--color-frame-size);
	color: transparent;
	border-radius: var(--color-frame-rounded);
	overflow: hidden;
	background-clip: padding-box;
}
.k-color-frame::after {
	border-radius: var(--color-frame-rounded);
	box-shadow: 0 0 0 1px inset hsla(0, 0%, var(--color-frame-darkness), 0.175);
	position: absolute;
	inset: 0;
	background: var(--color-frame-back);
	content: "";
}
</style>
