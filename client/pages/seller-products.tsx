import Layout from "@/components/Layout";
import Container from "@/components/Container";
import Head from 'next/head';
import {Avatar} from "@nextui-org/avatar";
import {Input} from "@nextui-org/input";
import Image from 'next/image';
import React, { useState, useRef } from 'react';
import { GetServerSideProps } from "next";
import { getSession } from "@/lib/session";
import { UserData } from "@/lib/models/User";
import { useForm, FieldError } from 'react-hook-form';
import {useEffect} from 'react';
import useSWRMutation from 'swr/mutation';
import useSWR from 'swr';
import { getFetcher, formDataFetcher } from "@/lib/api";
import { ToastContainer, toast } from 'react-toastify';
import { useRouter } from 'next/router';

const fetcher = (url: string) => fetch(url).then(res => res.json());

export const getServerSideProps: GetServerSideProps = async ({ req, res }) => {
  const session = await getSession(req, res);

  if (!session.user) {
    return {
      redirect: {
        destination: '/login', 
        permanent: false,
      },
    };
  }

  return {
    props: { user: session.user ,api_token: session.api_token}, 
  };
};


export default function SellerProduct({ user, api_token }: { user: UserData, api_token: string }) {

  const { data, error } = useSWR(`/backed/api/seller-products?api_token=${api_token}`, fetcher);

    const { register, handleSubmit, formState: { errors }, reset } = useForm();
    useEffect(() => {
        if (user) {
          reset({
            name: user.name || '',
            email: user.email || '',
            avatar: user.avatar || ''
          });
        }
    }, [user, reset]);

    const [selectedFile, setSelectedFile] = useState<string | null>(null);
    const fileInputRef = useRef<HTMLInputElement | null>(null);

    const handleFileChange = (event:any) => {
        const file = event.target.files[0];
        if (file) {
            setSelectedFile(URL.createObjectURL(file));
        }
    };

    const handleButtonClick = () => {
        if (fileInputRef.current) {
            fileInputRef.current.click();
        }
    };

    const [updateProfileResult, setUpdateProfileResult] = useState<any>(null); 
    const onSubmit = async (values: any) => {
        const formData = new FormData();
        formData.append('name', values.name);
        formData.append('email', values.email);
        if (fileInputRef.current?.files && fileInputRef.current.files[0]) {
            formData.append('avatar', fileInputRef.current.files[0]);
        } 
        formData.forEach((value, key) => {
            console.log(key, value);
        });

        const result = await formDataFetcher('backed/api/profile',api_token, formData);
        setUpdateProfileResult(result);
    };


    return (
        <Layout mainClass="flex w-full h-auto items-center justify-center" user={user}>
            <Head>
                <title>個人資訊</title>
            </Head>
            <Container containerClass="flex flex-col max-w-screen-2xl  w-full px-6 sm:flex-row">
                <article className="w-full text-wrap p-5 sm:w-1/5 sm:mt-6">
                    <div className="font-medium flex items-center">
                        <button className="p-2">
                            <p>個人資訊</p>
                            <h1>Welcome, {user.name}</h1>
                        </button>
                    </div>
                </article>
                <div className="w-full border rounded-xl p-5 bg-white gap-3 sm:p-8 sm:mt-6">
                    <p className="text-center text-3xl font-bold mb-5 mx-4 mt-4">個人資訊</p>
                    <hr />
                    <div className="flex flex-col sm:flex-row justify-center  border rounded-xl">
                        <table className="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" className="px-6 py-3">
                                        Product name
                                    </th>
                                    <th scope="col" className="px-6 py-3">
                                        Color
                                    </th>
                                    <th scope="col" className="px-6 py-3">
                                        Category
                                    </th>
                                    <th scope="col" className="px-6 py-3">
                                        Price
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr className="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row" className="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        Apple MacBook Pro 17"
                                    </th>
                                    <td className="px-6 py-4">
                                        Silver
                                    </td>
                                    <td className="px-6 py-4">
                                        Laptop
                                    </td>
                                    <td className="px-6 py-4">
                                        $2999
                                    </td>
                                </tr>
                                <tr className="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row" className="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        Microsoft Surface Pro
                                    </th>
                                    <td className="px-6 py-4">
                                        White
                                    </td>
                                    <td className="px-6 py-4">
                                        Laptop PC
                                    </td>
                                    <td className="px-6 py-4">
                                        $1999
                                    </td>
                                </tr>
                                <tr className="bg-white dark:bg-gray-800">
                                    <th scope="row" className="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        Magic Mouse 2
                                    </th>
                                    <td className="px-6 py-4">
                                        Black
                                    </td>
                                    <td className="px-6 py-4">
                                        Accessories
                                    </td>
                                    <td className="px-6 py-4">
                                        $99
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </Container>
            
        </Layout>
    );
}
