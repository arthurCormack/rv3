import { all, call, delay, put, take, takeLatest } from 'redux-saga/effects'
import { actionTypes } from './actions'


function* simpleTestSaga() {
  console.log('simpleTestSaga was triggeered by a SIMPLE_TEST');
  yield delay(1000);
  console.log('after 1 seconds');
   yield delay(1000);
  console.log('after 2 seconds');
   yield delay(1000);
  console.log('after 3 seconds');
}

function* rootSaga() {
  console.log('rootSaga:')
  
  yield all([
    // call(runClockSaga),
    takeLatest(actionTypes.SIMPLE_TEST, simpleTestSaga),
  ])
}

export default rootSaga