import { HeaderCardListInterface } from './header-card-list-interface';
import { ConfigCardListAction } from './config-card-list-action-interface';

export class ConfigCardListInterface {
    header: HeaderCardListInterface[];
    data: any[];
    actions?: ConfigCardListAction[];
}