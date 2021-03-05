import { all, call, delay, put, take, takeLatest } from 'redux-saga/effects'
import { actionTypes } from './actions'
import { loadGeneralContentSaga } from "./sagas"

function* simpleTestSaga() {
  console.log('simpleTestSaga was triggeered by a SIMPLE_TEST');
  yield delay(1000);
  console.log("simpleTestSaga after 1 seconds")
   yield delay(1000);
  console.log("simpleTestSaga after 2 seconds")
   yield delay(1000);
  console.log("simpleTestSaga after 3 seconds")
}

function* rootSaga() {
  console.log('rootSaga:')
  
  yield all([
    // call(runClockSaga),
    takeLatest(actionTypes.GENERAL_CONTENT_LOAD_TRIGGER, loadGeneralContentSaga),

  ])
}

export default rootSaga