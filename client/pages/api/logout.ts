import { NextApiRequest, NextApiResponse } from "next";
import { getSession } from "@/lib/session";

export default async function logoutRoute(req: NextApiRequest, res: NextApiResponse) {
  const session = await getSession(req, res);

  if (req.method === "POST") {
    const session = await getSession(req, res);
    const { user, api_token } = session;

    if (!api_token) {
      return res.status(401).json({ message: "API token is missing" });
    }

    const host = process.env.HOST || 'http://localhost:8080';
    const response = await fetch(`${host}/api/logout`, {
      method: "POST",
      credentials: "include",
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${api_token}`
      },
      body: JSON.stringify({}),
    });

    const data = await response.json();

    if (response.ok) {
      if(data.result == "success"){
        session.destroy();
        res.status(200).json(data);
      } else {
        res.status(400).json(data);
      }

    } else {
      res.status(response.status).json({
        message: "Logout failed",
        error: data.error || "An unknown error occurred",
      });
    }
  } else {
    res.status(405).json({ message: "Method not allowed" });
  }
}
