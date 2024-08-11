/** @type {import('next').NextConfig} */
const host = process.env.HOST || 'http://localhost:3000';

const nextConfig = {
    images: {
      remotePatterns: [
        {
          protocol: 'https',
          hostname: 'mdbcdn.b-cdn.net',
          pathname: '/img/Photos/new-templates/bootstrap-registration/**',
        },
      ],
    },
    async rewrites() {
      return [
        {
          source: '/backed/api/:path*',
          destination: `${host}/api/:path*`,
          // destination: 'http://35.185.162.249:8080/:path*',
        },
      ];
    },
  };
  
  export default nextConfig;