import { Injectable } from '@nestjs/common'
import {
  InvalidUserException,
  UserInterface,
  UserValidatorInterface,
} from 'nestjs-oauth2-server'

@Injectable()
export class UserValidator implements UserValidatorInterface {
  async validate(username, password): Promise<UserInterface> {
    throw InvalidUserException.withUsernameAndPassword(username, password)
  }
}
