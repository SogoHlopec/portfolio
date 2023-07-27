import { ICar, ICars, IWinner, IWinners } from "./interfaces";

class Api {
  url: string;

  constructor() {
    this.url = "http://localhost:3000";
  }

  async getCars(page: number, limit = 7): Promise<ICars> {
    try {
      const response: Response = await fetch(
        `${this.url}/garage?_page=${page}&_limit=${limit}`
      );

      const cars: ICars = {
        items: await response.json(),
        count: await response.headers.get("X-Total-Count"),
      };
      return cars;
    } catch (error) {
      throw new Error("Error get Cars");
    }
  }

  async getCar(id: number): Promise<ICar> {
    try {
      const response: Response = await fetch(`${this.url}/garage/${id}`);
      const car: ICar = await response.json();
      return car;
    } catch (error) {
      throw new Error("Error get Car");
    }
  }

  async getWinners(page: number, limit = 10): Promise<IWinners> {
    try {
      const response: Response = await fetch(
        `${this.url}/winners?_page=${page}&_limit=${limit}`
      );
      const winners: IWinners = {
        items: await response.json(),
        count: await response.headers.get("X-Total-Count"),
      };
      return winners;
    } catch (error) {
      throw new Error("Error get Winners");
    }
  }

  async getWinner(id: number): Promise<IWinner> {
    try {
      const response: Response = await fetch(`${this.url}/winners?_id=${id}`);
      const winner: IWinner = await response.json();
      return winner;
    } catch (error) {
      throw new Error("Error get Winner");
    }
  }
}

const API: Api = new Api();
export { API };
