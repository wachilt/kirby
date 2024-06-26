<template>
	<k-lab-examples class="k-lab-helpers-examples">
		<k-box theme="text">
			<k-text>
				Access the following link helpers in your Vue components through
				<code>this.$helper.link</code>
			</k-text>
		</k-box>

		<k-lab-example label="$helper.link.detect()" script="detect">
			<k-text>
				<p>Detects the type of a link:</p>
				<!-- prettier-ignore -->
				<k-code language="javascript">this.$helper.link.detect(link): object</k-code>
			</k-text>

			<!-- @code -->
			<k-grid variant="fields">
				<k-column width="1/2">
					<h2>Input</h2>
					<k-input type="text" :value="detect" @input="detect = $event" />
				</k-column>
				<k-column width="1/2">
					<h2>Result</h2>
					<k-code language="javascript">{{
						$helper.link.detect(detect)
					}}</k-code>
				</k-column>
			</k-grid>
			<!-- @code-end -->
		</k-lab-example>

		<k-lab-example label="$helper.link.preview()" script="preview">
			<k-text>
				<p>Returns preview data for the link:</p>
				<!-- prettier-ignore -->
				<k-code language="javascript">this.$helper.link.preview(link): object</k-code>
			</k-text>

			<!-- @code -->
			<k-grid variant="fields">
				<k-column width="1/2">
					<h2>Input</h2>
					<k-input type="text" :value="preview" @input="preview = $event" />
				</k-column>
				<k-column width="1/2">
					<h2>Result</h2>
					<k-code language="javascript">{{ previewResult }}</k-code>
				</k-column>
			</k-grid>
			<!-- @code-end -->
		</k-lab-example>

		<k-lab-example label="$helper.link.types()" :code="false">
			<k-text>
				<p>Returns default types:</p>
				<!-- prettier-ignore -->
				<k-code language="javascript">this.$helper.link.types(keys): object</k-code>
			</k-text>

			<!-- @code -->
			<h2>Result</h2>
			<k-code language="javascript">{{ $helper.link.types() }}</k-code>
			<!-- @code-end -->
		</k-lab-example>
	</k-lab-examples>
</template>

<script>
/** @script: detect */
export const detect = {
	data() {
		return {
			detect: "https://getkirby.com"
		};
	}
};
/** @script-end */

/** @script: preview */
export const preview = {
	data() {
		return {
			preview: "https://getkirby.com",
			previewResult: null
		};
	},
	watch: {
		preview: {
			async handler() {
				const link = this.$helper.link.detect(this.preview);
				this.previewResult = await this.$helper.link.preview(link);
			},
			immediate: true
		}
	}
};
/** @script-end */

export default {
	mixins: [detect, preview]
};
</script>
