import { createSelector } from 'reselect';

import { initialState } from './reducer';

const selectContents = state => state.contents || initialState;

const makeSelectCurrentContentInstanceID = () =>
  createSelector(
    selectContents,
    (contents) => {
      return contents.currentContentInstanceID;
    }
  );

const makeSelectLoading = () =>
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
  makeSelectLoading,
  makeSelectNextContentInstanceIDBeingLoaded, 
};
// const makeSelectCurrentContentInstanceID