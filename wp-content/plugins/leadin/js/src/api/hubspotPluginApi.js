import Raven from '../lib/Raven';

function callInterframeMethod(method, ...args) {
  return window.leadinChildFrameConnection.promise.then(child =>
    Raven.context(child[method], args)
  );
}

export function getAuth() {
  return callInterframeMethod('leadinGetAuth');
}

export function searchForms(searchQuery = '') {
  return callInterframeMethod('leadinSearchForms', searchQuery);
}

export function getForm(formId) {
  return callInterframeMethod('leadinGetForm', formId);
}

export function monitorFormPreviewRender() {
  return callInterframeMethod('monitorFormPreviewRender');
}

export function monitorFormCreatedFromTemplate(type) {
  return callInterframeMethod('monitorFormCreatedFromTemplate', type);
}

export function monitorFormCreationFailed(error) {
  return callInterframeMethod('monitorFormCreationFailed', error);
}
