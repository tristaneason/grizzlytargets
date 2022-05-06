/**
 * Post Type Builder - Featured Image
 * 
 * @since 6.3.0
 */
( function ( wpI18n, wpBlocks, wpBlockEditor, wpComponents ) {
    "use strict";

    const __ = wpI18n.__,
        registerBlockType = wpBlocks.registerBlockType,
        InspectorControls = wpBlockEditor.InspectorControls,
        SelectControl = wpComponents.SelectControl,
        TextControl = wpComponents.TextControl,
        ToggleControl = wpComponents.ToggleControl,
        Disabled = wpComponents.Disabled,
        ServerSideRender = wp.serverSideRender;

    const PortoTBImage = function ( { attributes, setAttributes, name } ) {

        const content_type = document.getElementById( 'content_type' ).value;
        let content_type_value,
            attrs = { image_size: attributes.image_size, customClass: attributes.customClass, image_type: attributes.image_type };
        if ( content_type ) {
            attrs.content_type = content_type;
            content_type_value = document.getElementById( 'content_type_' + content_type );
            if ( content_type_value ) {
                content_type_value = content_type_value.value;
                attrs.content_type_value = content_type_value;
            }
        }
        return (
            <>
                <InspectorControls key="inspector">
                    <SelectControl
                        label={ __( 'Image Type', 'porto-functionality' ) }
                        value={ attributes.image_type }
                        options={ [
                            { label: __( 'Single image', 'porto-functionality' ), value: '' },
                            { label: __( 'Show secondary image on hover', 'porto-functionality' ), value: 'hover' },
                            { label: __( 'Slider', 'porto-functionality' ), value: 'slider' },
                            { label: __( 'Video & Image', 'porto-functionality' ), value: 'video' },
                            { label: __( 'Grid Gallery', 'porto-functionality' ), value: 'gallery' }
                        ] }
                        onChange={ ( value ) => { setAttributes( { image_type: value } ); } }
                        help={ __( 'Please select the image type.', 'porto-functionality' ) }
                    />
                    { 'custom' === attributes.add_link && (
                        <SelectControl
                            label={ __( 'Image Hover Effect', 'porto-functionality' ) }
                            value={ attributes.hover_effect }
                            options={ [
                                { label: __( 'None', 'porto-functionality' ), value: '' },
                                { label: __( 'Zoom In', 'porto-functionality' ), value: 'zoom' },
                                { label: __( 'Effect 1', 'porto-functionality' ), value: 'effect-1' },
                                { label: __( 'Effect 2', 'porto-functionality' ), value: 'effect-2' },
                                { label: __( 'Effect 3', 'porto-functionality' ), value: 'effect-3' },
                                { label: __( 'Effect 4', 'porto-functionality' ), value: 'effect-4' },
                            ] }
                            onChange={ ( value ) => { setAttributes( { hover_effect: value } ); } }
                        />
                    ) }
                    <SelectControl
                        label={ __( 'Add Link to Image', 'porto-functionality' ) }
                        value={ attributes.add_link }
                        options={ [ { 'label': __( 'Yes', 'porto-functionality' ), 'value': 'yes' }, { 'label': __( 'No', 'porto-functionality' ), 'value': 'no' }, { 'label': __( 'Custom Link', 'porto-functionality' ), 'value': 'custom' } ] }
                        onChange={ ( value ) => { setAttributes( { add_link: value } ); } }
                    />
                    { 'custom' === attributes.add_link && (
                        <TextControl
                            label={ __( 'Custom Link', 'porto-functionality' ) }
                            value={ attributes.custom_url }
                            onChange={ ( value ) => { setAttributes( { custom_url: value } ); } }
                            help={ __( 'Please input custom url.', 'porto-functionality' ) }
                        />
                    ) }
                    { 'custom' === attributes.add_link && attributes.custom_url && (
                        <SelectControl
                            label={ __( 'Link Target', 'porto-functionality' ) }
                            value={ attributes.link_target }
                            options={ [ { 'label': '_self', 'value': '' }, { 'label': '_blank', 'value': '_blank' } ] }
                            onChange={ ( value ) => { setAttributes( { link_target: value } ); } }
                        />
                    ) }
                    <SelectControl
                        label={ __( 'Image Size', 'porto-functionality' ) }
                        value={ attributes.image_size }
                        options={ porto_block_vars.image_sizes }
                        onChange={ ( value ) => { setAttributes( { image_size: value } ); } }
                    />
                </InspectorControls>
                <Disabled>
                    <ServerSideRender
                        block={ name }
                        attributes={ attrs }
                    />
                </Disabled>
            </>
        )
    }
    registerBlockType( 'porto-tb/porto-featured-image', {
        title: __( 'Featured Image', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-tb',
        attributes: {
            image_type: {
                type: 'string',
                default: '',
            },
            hover_effect: {
                type: 'string',
                default: '',
            },
            content_type: {
                type: 'string',
            },
            content_type_value: {
                type: 'string',
            },
            add_link: {
                type: 'string',
                default: 'yes',
            },
            custom_url: {
                type: 'string',
            },
            link_target: {
                type: 'string',
            },
            image_size: {
                type: 'string',
            }
        },
        edit: PortoTBImage,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.blockEditor, wp.components );