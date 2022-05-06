import React from 'react';
import { registerBlockType } from '@wordpress/blocks';
import SprocketIcon from '../Common/SprocketIcon';
import FormBlockEdit from './FormBlockEdit';
import FormBlockSave from './FormBlockSave';
import { connectionStatus, i18n } from '../../constants/leadinConfig';
import FormErrorHandler from './FormBlockEdit/FormErrorHandler';

const ConnectionStatus = {
  Connected: 'Connected',
  NotConnected: 'NotConnected',
};

export default function registerFormBlock() {
  const editComponent = props =>
    connectionStatus === ConnectionStatus.Connected ? (
      <FormBlockEdit {...props} />
    ) : (
      <FormErrorHandler status={401} />
    );

  registerBlockType('leadin/hubspot-form-block', {
    title: i18n.formBlockTitle,
    description: i18n.formBlockDescription,
    icon: SprocketIcon,
    category: 'leadin-blocks',
    attributes: {
      portalId: {
        type: 'string',
      },
      formId: {
        type: 'string',
      },
      formName: {
        type: 'string',
      },
    },
    edit: editComponent,
    save: props => <FormBlockSave {...props} />,
  });
}
