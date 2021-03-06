<template>
  <header :data-editable="editable" class="k-header">
    <k-headline tag="h1" size="huge">
      <span v-if="editable && $listeners.edit" class="k-headline-editable" @click="$emit('edit')">
        <slot />
        <k-icon type="edit" />
      </span>
      <slot v-else />
    </k-headline>
    <k-bar v-if="$slots.left || $slots.right" class="k-header-buttons">
      <slot slot="left" name="left" class="k-header-left" />
      <slot slot="right" name="right" class="k-header-right" />
    </k-bar>

    <k-tabs :tab="tab" :tabs="tabsWithBadges" theme="notice" />
  </header>
</template>

<script>

export default {
  props: {
    editable: Boolean,
    tab: String,
    tabs: {
      type: Array,
      default() {
        return []
      }
    }
  },
  computed:  {
    tabsWithBadges() {
      const changed = Object.keys(this.$store.getters['content/changes']());

      return this.tabs.map(tab => {

        // collect all fields per tab
        let fields = [];
        Object.values(tab.columns).forEach(column => {
          Object.values(column.sections).forEach(section => {
            if (section.type === "fields") {
              Object.keys(section.fields).forEach(field => {
                fields.push(field);
              })
            }
          })
        });

        // get count of changed fields in this tab
        tab.badge = fields.filter(field => changed.includes(field.toLowerCase())).length;

        return tab;
      });
    }
  }
}
</script>

<style lang="scss">
.k-header {
  border-bottom: 1px solid $color-border;
  margin-bottom: 2rem;
  padding-top: 4vh;
}
.k-header .k-headline {
  min-height: 1.25em;
  margin-bottom: .5rem;
  word-wrap: break-word;
}
.k-header .k-header-buttons {
  margin-top: -.5rem;
  height: 3.25rem;
}
.k-header .k-headline-editable {
  cursor: pointer;
}
.k-header .k-headline-editable .k-icon {
  color: $color-light-grey;
  opacity: 0;
  transition: opacity .3s;
  display: inline-block;

  [dir="ltr"] & {
    margin-left: .5rem;
  }

  [dir="rtl"] & {
    margin-right: .5rem;
  }
}
.k-header .k-headline-editable:hover .k-icon {
  opacity: 1;
}
</style>
