/**
 * Listing Source plugin for Craft CMS
 *
 * ListingSourceFieldType JS
 *
 * @author    Kurious Agency
 * @copyright Copyright (c) 2018 Kurious Agency
 * @link      https://kurious.agency
 * @package   ListingSource
 * @since     1.0.0
 */

;
(function($) {

    ListingSource = Garnish.Base.extend({

        $field: null,
        $typeSelect: null,
        $optionsHolder: null,
        $settingsHolder: null,
        $options: null,

        type: null,

        init: function(id) {
            this.$field = $('#' + id);

            this.$typeSelect = this.$field.find('.listingsource-type select');
            this.type = this.$typeSelect.val();

            this.$optionsHolder = this.$field.find('.listingsource-options');
            this.$settingsHolder = this.$field.find('.listingsource-settings');
            this.$options = this.$optionsHolder.find('.listingsource-option');

            this.addListener(this.$typeSelect, 'change', 'onChangeType');
        },

        onChangeType: function(e) {
            var $select = $(e.currentTarget);
            this.type = $select.val();

            if (this.type === '') {
                this.$optionsHolder.add(this.$settingsHolder).addClass('hidden');
            } else {
                this.$optionsHolder.add(this.$settingsHolder).removeClass('hidden');
            }

            this.$options.addClass('hidden');
            this.$options.filter('.listingsource-' + this.type).removeClass('hidden');
        }

    });

})(jQuery);