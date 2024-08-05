import Layout from "@/components/Layout";
import Head from 'next/head';

export default function Profile({ title }) {
    return (
        <Layout>
            <Head>
                <title>{title}</title>
            </Head>
            {/* <Container>
                <Marquee />
                <BannerCarousel />
                <HallList handleClick={setGames} />
                <GameList
                    games={games}
                    setIsLoginNotification={props.setIsLoginNotification}
                />
            </Container> */}
        </Layout>
    );
}
