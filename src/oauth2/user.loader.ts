import { Injectable } from '@nestjs/common'
import {
  InvalidUserException,
  UserInterface,
  UserLoaderInterface,
} from 'nestjs-oauth2-server'

@Injectable()
export class UserLoader implements UserLoaderInterface {
  async load(userId: string): Promise<UserInterface> {
    throw InvalidUserException.withId(userId)
  }
}
