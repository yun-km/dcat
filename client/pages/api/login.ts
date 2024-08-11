// import { getIronSession } from "iron-session";
import { NextApiRequest, NextApiResponse } from "next";
import { sessionOptions, getSession } from "@/lib/session";

export default async function loginRoute(req: NextApiRequest, res: NextApiResponse) {
  if (req.method === "POST") {
    const host = process.env.HOST || 'http://localhost:8080';
    const response = await fetch(`${host}/api/login`, {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(req.body),
    });

    const contentType = response.headers.get("content-type");

    if (response.ok) {
      const data = await response.json();
      const session = await getSession(req, res);

      if(data.result == "success"){
        session.api_token = data.content.api_token;
        session.user = data.content.user;

        await session.save();
        console.log("Session after save:", session);
        
        res.status(200).json(data);
      } else {
        res.status(400).json(data);
      }
    } else {
      const errorText = await response.text();
      console.error("Unexpected response:", errorText);

      res.status(response.status).json({
        message: "Login failed",
        error: "Received non-JSON response",
        details: errorText
      });
    }
  } else {
    res.status(405).json({ message: "Method not allowed" });
  }
}