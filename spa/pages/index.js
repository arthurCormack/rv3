import { useEffect } from 'react'
import { useDispatch } from 'react-redux'
import Link from 'next/link'
import { simpleTest } from '../actions';
import { wrapper } from '../store'
import { END } from 'redux-saga'

// import { useInjectReducer, useInjectSaga } from 'redux-injectors';

// import { simpleHomeReducer as reducer } from '../reducers';
// import { simpleHomeSaga as saga} from './sagas';

const key = 'home';

// import { startClock } from '../actions'
// import Examples from '../components/examples'

const Index = () => {
  const dispatch = useDispatch()
  // useEffect(() => {
  //   dispatch(startClock())
  // }, [dispatch])

  return (
    <>
      {/* <Examples /> */}
      Hello
      <Link href="/show-redux-state">
        <a>Click to see current Redux State</a>
      </Link>
    </>
  )
}

export const getServerSideProps = wrapper.getServerSideProps(async ({ store }) => {
  console.log('getServerSideProps()', store);
  store.dispatch(simpleTest())
  store.dispatch(END)
  // if (!store.getState().placeholderData) {
  //   store.dispatch(loadData())
  //   store.dispatch(END)
  // }

  await store.sagaTask.toPromise()
})

export default Index
