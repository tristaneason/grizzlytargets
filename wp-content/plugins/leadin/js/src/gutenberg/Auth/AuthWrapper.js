import React, { Fragment } from 'react';
import useAuth from './useAuth';
import LoadingBlock from '../Common/LoadingBlock';
import FormErrorHandler from '../FormBlock/FormBlockEdit/FormErrorHandler';

export default function AuthWrapper({ children }) {
  const { auth, loading } = useAuth();

  return loading ? (
    <LoadingBlock />
  ) : auth ? (
    <Fragment>{children}</Fragment>
  ) : (
    <FormErrorHandler status={401} />
  );
}
