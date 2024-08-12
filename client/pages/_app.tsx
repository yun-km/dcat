import type { AppProps } from 'next/app';
import { SWRConfig } from 'swr';
import '../app/globals.css';
import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

function MyApp({ Component, pageProps }: AppProps) {
  return (
    <SWRConfig
      value={{
        fetcher: (url) => fetch(url).then((res) => res.json()),
        onError: (error) => {
          console.error(error);
        },
      }}
    >
      <Component {...pageProps} />
      <ToastContainer
          position="top-right"
          autoClose={5000}
          hideProgressBar={false}
          newestOnTop={false}
          closeOnClick
          rtl={false}
          pauseOnFocusLoss
          draggable
          pauseOnHover
          theme="dark"
      />
    </SWRConfig>
  );
}

export default MyApp;
