(function () {
    var registerBlockType = wp.blocks.registerBlockType;
    var el                = wp.element.createElement;
    var Fragment          = wp.element.Fragment;
    var useSelect         = wp.data.useSelect;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var useBlockProps     = wp.blockEditor.useBlockProps;
    var PanelBody         = wp.components.PanelBody;
    var RangeControl      = wp.components.RangeControl;
    var SelectControl     = wp.components.SelectControl;

    registerBlockType('folkphotography/masonry-gallery', {
        title:       'Masonry Gallery',
        icon:        'format-gallery',
        category:    'media',
        description: 'Display a masonry grid of portfolio or blog photos from a selected category.',

        attributes: {
            termId:   { type: 'number', default: 0 },
            taxonomy: { type: 'string', default: 'portfolio_category' },
            columns:  { type: 'number', default: 3 },
            count:    { type: 'number', default: 12 },
        },

        edit: function (props) {
            var attributes   = props.attributes;
            var setAttributes = props.setAttributes;
            var termId       = attributes.termId;
            var taxonomy     = attributes.taxonomy;
            var columns      = attributes.columns;
            var count        = attributes.count;

            var blockProps = useBlockProps({ className: 'folk-masonry-block-editor-preview' });

            var terms = useSelect(function (select) {
                return select('core').getEntityRecords('taxonomy', taxonomy, { per_page: -1, _fields: 'id,name' });
            }, [taxonomy]);

            var termOptions = [{ label: 'All', value: 0 }].concat(
                (terms || []).map(function (term) {
                    return { label: term.name, value: term.id };
                })
            );

            var taxonomyOptions = [
                { label: 'Portfolio Categories', value: 'portfolio_category' },
                { label: 'Blog Categories',      value: 'category' },
            ];

            // Friendly label for the preview caption
            var termLabel = 'All';
            if (termId) {
                var found = termOptions.find(function (t) { return t.value === termId; });
                termLabel = found ? found.label : 'Selected category';
            }

            return el(Fragment, null,
                el(InspectorControls, null,
                    el(PanelBody, { title: 'Gallery Settings', initialOpen: true },
                        el(SelectControl, {
                            label:    'Source taxonomy',
                            value:    taxonomy,
                            options:  taxonomyOptions,
                            onChange: function (val) { setAttributes({ taxonomy: val, termId: 0 }); },
                        }),
                        el(SelectControl, {
                            label:    'Category',
                            value:    termId,
                            options:  termOptions,
                            onChange: function (val) { setAttributes({ termId: parseInt(val, 10) }); },
                        }),
                        el(RangeControl, {
                            label:    'Columns',
                            value:    columns,
                            min:      2,
                            max:      4,
                            onChange: function (val) { setAttributes({ columns: val }); },
                        }),
                        el(RangeControl, {
                            label:    'Number of photos',
                            value:    count,
                            min:      4,
                            max:      30,
                            onChange: function (val) { setAttributes({ count: val }); },
                        })
                    )
                ),
                el('div', blockProps,
                    el('div', { className: 'folk-masonry-block-placeholder' },
                        el('span', { className: 'dashicons dashicons-format-gallery', style: { fontSize: '2rem', display: 'block', marginBottom: '0.5rem' } }),
                        el('strong', null, 'Masonry Gallery'),
                        el('p', { style: { margin: '0.25rem 0 0', fontSize: '0.85em', opacity: 0.7 } },
                            termLabel + ' • ' + columns + ' cols • ' + count + ' photos'
                        )
                    )
                )
            );
        },

        // Server-side rendered — save returns null.
        save: function () { return null; },
    });
})();
