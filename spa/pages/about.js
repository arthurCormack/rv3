import { useEffect } from 'react';
import { useDispatch } from 'react-redux';
import { END } from 'redux-saga';
import { wrapper } from '../store';
import { pageLoadTrigger, pageLoadStarted, pageLoadSuccess, pageLoadFailure } from '../actions';
import Page from '../components/page';


import Head from 'next/head'


const About = () => {
  // const dispatch = useDispatch();

  // useEffect(() => {
  //   dispatch(startClock());
  // }, [dispatch]);

  // return <Page title="Index Page" linkTo="/other" NavigateTo="Other Page" />
  return (
    <div>
      <Head>
        <title>This page has a title 🤔</title>
        <meta charSet="utf-8" />
        <meta name="viewport" content="initial-scale=1.0, width=device-width" />
      </Head>
      This is the about.js
    </div>
  );
}



export const getServerSideProps = wrapper.getStaticProps(async ({ store }) => {
  // store.dispatch(tickClock(false))

  if (!store.getState().page.data) {
    store.dispatch(pageLoadTrigger());// this is a trigger; a saga takes it and then loads data from an api endpoint using it.
    store.dispatch(END);
  }

  await store.sagaTask.toPromise();
});

export default About
