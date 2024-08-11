import Layout from "@/components/Layout";
import Container from "@/components/Container";
import Head from 'next/head';
import {Avatar} from "@nextui-org/avatar";
import {Input} from "@nextui-org/input";
import Image from 'next/image';
import React from 'react';

export default function Profile({ title }: { title: string }) {
    const fileInputRef = React.createRef<HTMLInputElement>();

    const handleButtonClick = () => {
      if (fileInputRef.current) {
        fileInputRef.current.click();
      }
    };

    return (
        <Layout mainClass="flex w-full h-auto items-center justify-center">
            <Head>
                <title>個人資訊</title>
            </Head>
            <Container containerClass="flex flex-col max-w-screen-lg  w-full px-6 sm:flex-row">
                <article className="w-full text-wrap p-5 sm:w-1/5 sm:mt-6">
                    <div className="font-medium flex items-center">
                        <button className="p-2">
                            <p>個人資訊</p>
                        </button>
                    </div>
                </article>
                <div className="w-full border rounded-xl p-5 bg-white gap-3 sm:w-4/5 sm:p-8 sm:mt-6">
                    <p className="text-center text-3xl font-bold mb-5 mx-4 mt-4">個人資訊</p>
                    <hr />
                    <div className="flex flex-col sm:flex-row justify-center m-6">
                        <div className="w-full max-w-md sm:w-2/3 order-2 sm:order-1">
                            <form className="mx-4" method="POST" >
                                {/* @csrf */}
                                <div className="flex items-center mb-4">
                                    <label className="fas fa-key text-lg me-3"></label>
                                    <div className="flex-grow">
                                        <Input variant="underlined" type="text" id="name" required name="userName" label="Name" autoComplete="new-name"/>
                                    </div>
                                </div>

                                <div className="flex items-center mb-4">
                                    <label className="fas fa-lock text-lg me-3"></label>
                                    <div className="flex-grow">
                                        {/* <Input variant="underlined" type="password" id="userPassword" className="form-control" required name="password" placeholder="NewPassword" /> */}
                                        <Input type="email" variant="underlined" label="Email" required name="password"/>
                                    </div>
                                </div>

                                <div className="flex items-center mb-4">
                                    <label className="fas fa-key text-lg me-3"></label>
                                    <div className="flex-grow">
                                        <Input variant="underlined" type="password" id="OldPassword" required name="old_password" label="OldPassword" autoComplete="new-password"/>
                                    </div>
                                </div>

                                <div className="flex items-center mb-4">
                                    <label className="fas fa-lock text-lg me-3"></label>
                                    <div className="flex-grow">
                                        <Input variant="underlined" type="password" id="userRePassword" required name="password_confirmation" label="rePassword"/>
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
                            <Avatar isBordered src="https://i.pravatar.cc/150?u=a04258114e29026708c" className="w-20 h-20 sm:w-28 sm:h-28 text-large" />
                            <input
                                type="file"
                                id="file_input"
                                ref={fileInputRef}
                                aria-label="avatar"
                                style={{ display: 'none' }}
                            />
                            <button
                                onClick={handleButtonClick} type="submit"
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
