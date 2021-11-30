import { makeProxyRequest } from './wordpressApiClient';

export function fetchForms(searchQuery = '', offset = 0, limit = 10) {
  const queryParams = {
    offset,
    limit,
    formTypes: ['HUBSPOT'],
  };

  if (searchQuery) {
    queryParams.name__contains = searchQuery;
  }

  const formsPath = `/forms/v2/forms`;

  return makeProxyRequest('get', formsPath, {}, queryParams).then(forms => {
    const filteredForms = [];

    forms.forEach(currentForm => {
      const { guid, name } = currentForm;
      filteredForms.push({ name, guid });
    });

    return filteredForms;
  });
}
