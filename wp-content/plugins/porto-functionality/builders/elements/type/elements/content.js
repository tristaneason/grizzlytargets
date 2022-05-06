/**
 * Post Type Builder - Content
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
        RangeControl = wpComponents.RangeControl,
        ToggleControl = wpComponents.ToggleControl,
        Disabled = wpComponents.Disabled,
        PanelBody = wpComponents.PanelBody,
        ServerSideRender = wp.serverSideRender,
        PortoTypographyControl = window.portoTypographyControl;

    const PortoTBContent = function ( { attributes, setAttributes, name } ) {

        const content_type = document.getElementById( 'content_type' ).value;
        let content_type_value,
            attrs = { content_display: attributes.content_display, excerpt_length: attributes.excerpt_length, strip_html: attributes.strip_html };
        if ( content_type ) {
            attrs.content_type = content_type;
            content_type_value = document.getElementById( 'content_type_' + content_type );
            if ( content_type_value ) {
                content_type_value = content_type_value.value;
                attrs.content_type_value = content_type_value;
            }
        }

        let internalStyle = '',
            font_settings = Object.assign( {}, attributes.font_settings );

        if ( attributes.alignment || attributes.font_settings ) {
            const fontAtts = attributes.font_settings;
            if ( attributes.alignment || fontAtts.fontFamily || fontAtts.fontSize || fontAtts.fontWeight || fontAtts.textTransform || fontAtts.lineHeight || fontAtts.letterSpacing || fontAtts.color ) {
                internalStyle += '.tb-content {';
                if ( attributes.alignment ) {
                    internalStyle += 'text-align:' + attributes.alignment + ';';
                }
                if ( fontAtts.fontFamily ) {
                    internalStyle += 'font-family:' + fontAtts.fontFamily + ';';
                }
                if ( fontAtts.fontSize ) {
                    let unitVal = fontAtts.fontSize;
                    const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                    if ( ! unit ) {
                        unitVal += 'px';
                    }
                    internalStyle += 'font-size:' + unitVal + ';';
                }
                if ( fontAtts.fontWeight ) {
                    internalStyle += 'font-weight:' + fontAtts.fontWeight + ';';
                }
                if ( fontAtts.textTransform ) {
                    internalStyle += 'text-transform:' + fontAtts.textTransform + ';';
                }
                if ( fontAtts.lineHeight ) {
                    let unitVal = fontAtts.lineHeight;
                    const unit = unitVal.trim().replace( /[0-9.]/g, '' );
                    if ( ! unit && Number( unitVal ) > 3 ) {
                        unitVal += 'px';
                    }
                    internalStyle += 'line-height:' + unitVal + ';';
                }
                if ( fontAtts.letterSpacing ) {
                    let unitVal = fontAtts.letterSpacing;
                    const unit = unitVal.trim().replace( /[0-9.-]/g, '' );
                    if ( ! unit ) {
                        unitVal += 'px';
                    }
                    internalStyle += 'letter-spacing:' + unitVal + ';';
                }
                if ( fontAtts.color ) {
                    internalStyle += 'color:' + fontAtts.color;
                }
                internalStyle += '}';
            }
        }

        return (
            <>
                <InspectorControls key="inspector">
                    <PanelBody title={ __( 'Layout', 'porto-functionality' ) } initialOpen={ true }>
                        <SelectControl
                            label={ __( 'Content Display', 'porto-functionality' ) }
                            value={ attributes.content_display }
                            options={ [ { 'label': __( 'Excerpt', 'porto-functionality' ), 'value': 'excerpt' }, { 'label': __( 'Content', 'porto-functionality' ), 'value': 'content' } ] }
                            onChange={ ( value ) => { setAttributes( { content_display: value } ); } }
                        />
                        { 'excerpt' === attributes.content_display && (
                            <RangeControl
                                label={ __( 'Excerpt Length', 'porto-functionality' ) }
                                value={ attributes.excerpt_length }
                                min="1"
                                max="100"
                                onChange={ ( value ) => { setAttributes( { excerpt_length: value } ); } }
                            />
                        ) }
                    </PanelBody>
                    <PanelBody title={ __( 'Style', 'porto-functionality' ) } initialOpen={ false }>
                        <SelectControl
                            label={ __( 'Alignment', 'porto-functionality' ) }
                            value={ attributes.alignment }
                            options={ [ { 'label': __( 'Inherit', 'porto-functionality' ), 'value': '' }, { 'label': __( 'Left', 'porto-functionality' ), 'value': 'left' }, { 'label': __( 'Center', 'porto-functionality' ), 'value': 'center' }, { 'label': __( 'Right', 'porto-functionality' ), 'value': 'right' }, { 'label': __( 'Justify', 'porto-functionality' ), 'value': 'justify' } ] }
                            onChange={ ( value ) => { setAttributes( { alignment: value } ); } }
                        />
                        <PortoTypographyControl
                            label={ __( 'Typography', 'porto-functionality' ) }
                            value={ font_settings }
                            options={ { } }
                            onChange={ ( value ) => {
                                setAttributes( { font_settings: value } );
                            } }
                        />
                    </PanelBody>
                </InspectorControls>
                <Disabled>
                    { internalStyle && (
                        <style>
                            { internalStyle }
                        </style>
                    ) }
                    <ServerSideRender
                        block={ name }
                        attributes={ attrs }
                    />
                </Disabled>
            </>
        )
    }
    registerBlockType( 'porto-tb/porto-content', {
        title: __( 'Content', 'porto-functionality' ),
        icon: 'porto',
        category: 'porto-tb',
        attributes: {
            content_display: {
                type: 'string',
                default: 'excerpt',
            },
            excerpt_length: {
                type: 'int',
                default: 50,
            },
            content_type: {
                type: 'string',
            },
            content_type_value: {
                type: 'string',
            },
            alignment: {
                type: 'string',
            },
            font_settings: {
                type: 'object',
            },
        },
        edit: PortoTBContent,
        save: function () {
            return null;
        }
    } );
} )( wp.i18n, wp.blocks, wp.blockEditor, wp.components );