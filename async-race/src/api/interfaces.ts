export interface ICar {
  name: string;
  color: string;
  id: number;
}

export interface ICars {
  items: ICar[];
  count: string | null;
}

export interface IWinner {
  id: number;
  wins: number;
  time: number;
}

export interface IWinners {
  items: IWinner[];
  count: string | null;
}
