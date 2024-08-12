import { SessionOptions, getIronSession } from "iron-session";
import { NextApiRequest, NextApiResponse } from "next";
import { UserData } from "./models/User";

export interface SessionData {
  user?: UserData;
  api_token?: string;
}

export const sessionOptions: SessionOptions = {
  password: process.env.SESSION_SECRET as string,
  cookieName: "dcat/next.js",
  cookieOptions: {
    secure: process.env.NODE_ENV === "development",
  },
};

export async function getSession(req: any, res: any) {
  const session = await import('iron-session').then(({ getIronSession }) =>
    getIronSession<SessionData>(req, res, sessionOptions)
  );
  return session;
}