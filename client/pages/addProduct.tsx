import Layout from "@/components/Layout";
import Container from "@/components/Container";
import Head from 'next/head';
import {Avatar} from "@nextui-org/avatar";
import {Input} from "@nextui-org/input";
import Image from 'next/image';
import React from 'react';

export default function addProduct({ title }: { title: string }) {
    const fileInputRef = React.createRef();

    const handleButtonClick = () => {
      fileInputRef.current.click();
    };

    return (
        <Layout mainClass="flex w-full h-auto items-center justify-center">
            <Head>
                <title>個人資訊</title>
            </Head>
            <Container containerClass="flex flex-col max-w-screen-xl  w-full px-6 sm:flex-row">
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
                    <div className="flex flex-col sm:flex-row justify-center pt-3 sm:p-6">
                        <div className="w-full">
                            <form className="mx-4" method="POST" >
                                {/* @csrf */}
                                <div className="grid grid-cols-4 sm:gap-4">
                                     <label htmlFor="product_name" className="mb-2 text-sm font-medium text-gray-900 col-start-1">Last name</label>
                                     <input type="text" id="product_name" className="col-start-3 col-end-5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="請填入商品名稱" required />
                                </div>

                                <div className="flex flex-col sm:flex-row sm:items-center mb-4 sm:gap-6">
                                    <label htmlFor="description" className="mb-2 text-sm font-medium text-gray-900 basis-1/4">description</label>
                                    <input type="text" id="description" className="basis-3/4 col-end-5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="請填入商品名稱" required />
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
                       
                    </div>
                </div>
            </Container>
            
        </Layout>
    );
}
