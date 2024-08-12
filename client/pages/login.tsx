import Layout from "@/components/Layout";
import Container from "@/components/Container";
import Head from 'next/head';
import Image from 'next/image';
import {useEffect} from 'react';
import useSWRMutation from 'swr/mutation';
import { useForm, FieldError } from 'react-hook-form';
import { UserData } from "@/lib/models/User";
import { postFetcher } from "@/lib/api";
import { useRouter } from 'next/router';
import { GetServerSideProps } from "next";
import { getSession } from "@/lib/session";

export const getServerSideProps: GetServerSideProps = async ({ req, res }) => {
  const session = await getSession(req, res);

  return {
    props: { user: session.user || null  }, 
  };
};

export default function Login({ user }: { user: UserData }) {
  const { register, handleSubmit, formState: { errors } } = useForm();
  const { trigger, data:loginResult, error, isMutating } = useSWRMutation('/api/login', postFetcher);
  const router = useRouter();
  const onSubmit = async (data: any) => {
    await trigger(data);
  };

  useEffect(() => {
    if (loginResult?.result == "success") {
      router.reload();
      // router.push('/profile')
    }
  }, [loginResult, router]);

    return (
        <Layout mainClass="flex w-full h-auto items-center justify-center" user={user}>
            <Head>
                <title>個人資訊</title>
            </Head>
            <Container containerClass="flex flex-col max-w-screen-lg  w-full px-6 sm:flex-row">
              <div className="bg-white rounded-lg shadow-lg p-8 mt-8 w-full">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                  <div>
                    <p className="text-center text-2xl font-bold mb-5">Login</p>

                    <form onSubmit={handleSubmit(onSubmit)}>

                      <div className="flex items-center gap-4 justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="size-6">
                          <path strokeLinecap="round" strokeLinejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                          <input
                            type="email"
                            id="email"
                            className="bg-gray-50 border border-gray-300 text-sm rounded-lg  p-2.5"
                            placeholder="Email"
                            {...register('email', { required: 'Email is required' })}
                          />
                          {errors.email && <p>{(errors.email as FieldError).message}</p>}
                        </div>

                      <div className="flex items-center gap-4 justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="size-6">
                          <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>

                        <input
                          type="password"
                          id="password"
                          className="bg-gray-50 border border-gray-300 text-sm rounded-lg  p-2.5"
                          placeholder="Password"
                          {...register('password', { required: 'Password is required' })}
                        />
                        {errors.password && <p>{(errors.password as FieldError).message}</p>}
                      </div>

                      <div className="flex justify-center">
                        <button
                          type="submit" disabled={isMutating}
                          className="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600"
                        >
                          Login
                        </button>
                      </div>
                      <p className="text-center text-sm font-bold mt-4">
                       {" Don't have an account?"}
                        <a href="/register" className="text-red-500 hover:underline ml-1">
                          Register
                        </a>
                      </p>
                    </form>
                  </div>

                  <div className="flex justify-center items-center">
                    <Image
                      src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-registration/draw1.webp"
                      className="rounded-lg shadow-md"
                      alt="Sample image"
                      width={500} 
                      height={500} 
                    />
                  </div>
                </div>
              </div>
            </Container>
            
        </Layout>
    );
}
