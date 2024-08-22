// pages/callback.js
import { sessionOptions, getSession } from "@/lib/session";
export async function getServerSideProps(context) {
    const { token, user } = context.query;

    if (token && user) {
        const session = await getSession(context.req, context.res);
        session.api_token = token;
        session.user = JSON.parse(decodeURIComponent(user));
        await session.save();
        console.log("Session after save:", session);
        return {
            redirect: {
                destination: '/profile',
                permanent: false,
            },
        };
    }

    return {
        redirect: {
            destination: '/login',
            permanent: false,
        },
    };
}

export default function CallbackPage() {
    return <div>Redirecting...</div>;
}
