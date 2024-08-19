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
import { getFetcher, formDataFetcher } from "@/lib/api";
import { ToastContainer, toast } from 'react-toastify';
import { useRouter } from 'next/router';

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


export default function Profile({ user, api_token }: { user: UserData, api_token: string }) {
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
    const { trigger: profileTrigger, data: profileData, error, isMutating } = useSWRMutation('/backed/api/profile', formDataFetcher);

    const [updateProfileResult, setUpdateProfileResult] = useState<any>(null); 
    const onSubmit = async (values: any) => {
        const formData = new FormData();
        formData.append('name', values.name);
        formData.append('email', values.email);
        formData.append('api_token',api_token);
        if (fileInputRef.current?.files && fileInputRef.current.files[0]) {
            formData.append('avatar', fileInputRef.current.files[0]);
        } 
        formData.forEach((value, key) => {
            console.log(key, value);
        });
        profileTrigger(formData);
    };

    useEffect(() => {
        setUpdateProfileResult(profileData);
    },[profileData])

    const { trigger, data:updateSessionData } = useSWRMutation('/api/updateUser', getFetcher);

    useEffect(() => {
        if (updateProfileResult?.result == "success") {
            toast.success('更新成功！')
            trigger();
        }
    }, [updateProfileResult]);

    const router = useRouter();
    useEffect(() => {
        if (updateSessionData?.result == "success") {
            router.reload();
        }
    }, [updateSessionData]);

    return (
        <Layout mainClass="flex w-full h-auto items-center justify-center" user={user}>
            <Head>
                <title>個人資訊</title>
            </Head>
            <Container containerClass="flex flex-col max-w-screen-lg  w-full px-6 sm:flex-row sm:mt-14">
                <article className="w-full text-wrap p-5 sm:w-1/5 sm:mt-6">
                    <div className="font-medium flex items-center">
                        <button className="p-2">
                            <p>個人資訊</p>
                            <h1>Welcome, {user.name}</h1>
                        </button>
                    </div>
                </article>
                <div className="w-full border rounded-xl p-5 bg-white gap-3 sm:w-4/5 sm:p-8 sm:mt-6">
                    <p className="text-center text-3xl font-bold mb-5 mx-4 mt-4">個人資訊</p>
                    <hr />
                    <div className="flex flex-col sm:flex-row justify-center m-6">
                        <div className="w-full max-w-md sm:w-2/3 order-2 sm:order-1">
                            <form className="mx-4" onSubmit={handleSubmit(onSubmit)} >
                                {/* @csrf */}
                                <div className="flex items-center mb-4">
                                    <label className="fas fa-key text-lg me-3"></label>
                                    <div className="flex-grow">
                                        <Input 
                                            variant="underlined" type="text" id="name" label="Name" size="lg"
                                            {...register('name', { required: 'name is required' })}
                                        />
                                        {errors.name && <p>{(errors.name as FieldError).message}</p>}
                                    </div>
                                </div>

                                <div className="flex items-center mb-4">
                                    <label className="fas fa-lock text-lg me-3"></label>
                                    <div className="flex-grow">
                                        <Input 
                                            isDisabled type="email" variant="underlined" label="Email" size="lg"
                                            {...register('email', { required: 'Email is required' })}
                                        />
                                        {errors.email && <p>{(errors.email as FieldError).message}</p>}
                                    </div>
                                </div>

                                <div className="flex items-center mb-4">
                                    <label className="fas fa-key text-lg me-3"></label>
                                    <div className="flex-grow">
                                        <Input variant="underlined" size="lg" type="password" id="OldPassword" name="old_password" label="OldPassword" />
                                    </div>
                                </div>

                                <div className="flex items-center mb-4">
                                    <label className="fas fa-lock text-lg me-3"></label>
                                    <div className="flex-grow">
                                        <Input variant="underlined" size="lg" type="password" id="userRePassword" name="password_confirmation" label="rePassword"/>
                                    </div>
                                </div>

                                <div className="flex justify-center mb-0 sm:mt-8">
                                <button type="submit" className="bg-blue-500 text-white py-2 px-4 rounded">
                                    更新個人資訊
                                </button>
                                </div>
                            </form>
                        </div>
                        <div className="w-full justify-center sm:flex-col gap-8 flex sm:w-1/3 items-center order-1  sm:order-2">
                            <Avatar isBordered src={selectedFile || `/backed/images/avatars/${user.avatar}`} className="w-20 h-20 sm:w-28 sm:h-28 text-large" />
                            <input
                                type="file"
                                id="file_input"
                                ref={fileInputRef}
                                aria-label="avatar"
                                style={{ display: 'none' }}
                                onChange={handleFileChange}
                            />
                            <button
                                onClick={handleButtonClick} type="submit" disabled={isMutating}
                                className="bg-blue-500 text-white py-2 px-4 rounded"
                            >
                                選擇檔案
                            </button>
                        </div>
                    </div>
                </div>
            </Container>
            
        </Layout>
    );
}
