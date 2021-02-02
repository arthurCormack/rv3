import { createSelector } from 'reselect';

import { initialState } from './rootReducer';

const selectContents = state => state.contents || initialState;

const makeSelectCurrentContentInstanceID = () =>
  createSelector(
    selectContents,
    (contents) => {
      return contents.currentContentInstanceID;
    }
  );

const makeSelectContentLoading = () =>
  createSelector(
    selectContents,
    (contents) => {
      return contents.loading;
    }
  );

const makeSelectNextContentInstanceIDBeingLoaded = () =>
  createSelector(
    selectContents,
    (contents) => {
      return contents.nextContentInstanceIDBeingLoaded;
    }
  );

export {
  makeSelectCurrentContentInstanceID,
  makeSelectContentLoading,
  makeSelectNextContentInstanceIDBeingLoaded, 
};
// const makeSelectCurrentContentInstanceID

