import React, { useState } from 'react';
import debounce from 'lodash/debounce';
import {
  monitorFormCreatedFromTemplate,
  monitorFormCreationFailed,
  searchForms,
} from '../../../api/hubspotPluginApi';
import {
  createForm,
  fetchForms as searchFormsOAuth,
} from '../../../api/hubspotApiClient';
import useForm from './useForm';
import FormSelector from './FormSelector';
import FormErrorHandler from './FormErrorHandler';
import LoadingBlock from '../../Common/LoadingBlock';
import { oauth } from '../../../constants/leadinConfig';
import {
  DEFAULT_OPTIONS,
  getFormDef,
  isDefaultForm,
} from '../../../constants/defaultFormOptions';

const mapForm = form => ({
  label: form.name,
  value: form.guid,
});

export default function FormSelect({ formId, formName, handleChange }) {
  const { form, loading, setLoading } = useForm(formId, formName);
  const [searchformError, setSearchFormError] = useState(null);

  const searchFormMethod = oauth ? searchFormsOAuth : searchForms;

  const loadOptions = debounce(
    (search, callback) => {
      searchFormMethod(search)
        .then(forms => callback([...forms.map(mapForm), DEFAULT_OPTIONS]))
        .catch(error => setSearchFormError(error));
    },
    300,
    { trailing: true }
  );

  const defaultOptions = form && !oauth ? [mapForm(form)] : true;
  const value = form ? mapForm(form) : null;

  const handleLocalChange = option => {
    if (isDefaultForm(option.value)) {
      setLoading(true);
      monitorFormCreatedFromTemplate(option.value);
      createForm(getFormDef(option.value))
        .then(({ guid, name }) => handleChange({ value: guid, label: name }))
        .catch(error => {
          setSearchFormError(error);
          monitorFormCreationFailed({ ...error, type: option.value });
        })
        .finally(() => setLoading(false));
    } else {
      handleChange(option);
    }
  };

  const formApiError = oauth && searchformError;
  return loading ? (
    <LoadingBlock />
  ) : !formApiError ? (
    <FormSelector
      defaultOptions={defaultOptions}
      loadOptions={loadOptions}
      onChange={option => handleLocalChange(option)}
      value={value}
    />
  ) : (
    <FormErrorHandler
      status={formApiError.status}
      resetErrorState={() => setSearchFormError(null)}
    />
  );
}
