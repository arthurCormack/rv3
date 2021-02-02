import Link from 'next/link';
import { useSelector } from 'react-redux';

// how do we want to store state now? single item content, stack of items content, stack of chunks of teasers ...
// but how do we want to organize the structure of content in state?
// when we are in a stack, we want to establish the stack, or add to the stack.
// but we also want, on every location change to check if we are in the same stack/context or not.
// 

function PostStack({ linkTo, NavigateTo, title }) {
  
  // const placeholderData = useSelector((state) => state.placeholderData)
  // const error = useSelector((state) => state.error)
  // const light = useSelector((state) => state.light)
  // const lastUpdate = useSelector((state) => state.lastUpdate)


  return (
    <div>
      <h1>{title}</h1>
      <Clock lastUpdate={lastUpdate} light={light} />
      <Counter />
      <nav>
        <Link href={linkTo}>
          <a>Navigate: {NavigateTo}</a>
        </Link>
      </nav>
      {placeholderData && (
        <pre>
          <code>{JSON.stringify(placeholderData, null, 2)}</code>
        </pre>
      )}
      {error && <p style={{ color: 'red' }}>Error: {error.message}</p>}
    </div>
  )
}

export default PostStack;
