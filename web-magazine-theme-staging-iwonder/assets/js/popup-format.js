(function (wp) {

    const registerFormatType =
        wp.richText.registerFormatType;

    const applyFormat =
        wp.richText.applyFormat;

    const getActiveFormat =
        wp.richText.getActiveFormat;

    const RichTextToolbarButton =
        wp.blockEditor.RichTextToolbarButton;

    const Modal =
        wp.components.Modal;

    const TextareaControl =
        wp.components.TextareaControl;

    const Button =
        wp.components.Button;

    const useState =
        wp.element.useState;

    registerFormatType('custom/popup-link', {

        title: 'Tooltip Link',

        tagName: 'a',

        className: 'popup-link',

        attributes: {

            href: 'href',

            popupContent:
                'data-popup-content',

        },

        edit: function (props) {

            const [isOpen, setOpen] =
                useState(false);

            const [popupContent,
                setPopupContent] =
                useState('');

            function openModal() {

                const activeFormat =
                    getActiveFormat(
                        props.value,
                        'custom/popup-link'
                    );

                let existingContent = '';

                if (
                    activeFormat &&
                    activeFormat.attributes &&
                    activeFormat.attributes
                        .popupContent
                ) {

                    existingContent =
                        activeFormat.attributes
                            .popupContent;

                }

                setPopupContent(
                    existingContent
                );

                setOpen(true);

            }

            function savePopup() {

                props.onChange(

                    applyFormat(
                        props.value,
                        {

                            type:
                                'custom/popup-link',

                            attributes: {

                                href:
                                    'javascript:void(0)',

                                popupContent:
                                    popupContent,

                            },

                        }
                    )

                );

                setOpen(false);

            }

            return wp.element.createElement(

                wp.element.Fragment,

                {},

                wp.element.createElement(
                    RichTextToolbarButton,
                    {

                        icon:
                            'welcome-view-site',

                        title:
                            'Tooltip Link',

                        isActive:
                            props.isActive,

                        onClick:
                            openModal,

                    }
                ),

                isOpen &&
                wp.element.createElement(

                    Modal,

                    {

                        title:
                            'Tooltip Content',

                        onRequestClose:
                            function () {

                                setOpen(false);

                            },

                    },

                    wp.element.createElement(
                        TextareaControl,
                        {

                            label:
                                'Enter Tooltip Content',

                            value:
                                popupContent,

                            rows: 8,

                            onChange:
                                function (
                                    value
                                ) {

                                    setPopupContent(
                                        value
                                    );

                                },

                        }
                    ),

                    wp.element.createElement(
                        Button,
                        {

                            variant:
                                'primary',

                            style: {

                                marginTop:
                                    '15px',

                            },

                            onClick:
                                savePopup,

                        },

                        'Save Tooltip'
                    )

                )

            );

        },

    });

})(window.wp);

