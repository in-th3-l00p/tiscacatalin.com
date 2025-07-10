---
layout: "../../layouts/PostLayout.astro"
title: "TypeScript Best Practices: Write Better, Safer Code"
description: "Discover essential TypeScript best practices that will help you write more maintainable, type-safe, and robust applications."
pubDate: "2024-02-05"
updatedDate: "2024-02-08"
---

TypeScript has revolutionized how we write JavaScript by adding static typing and modern language features. However, to truly benefit from TypeScript, you need to follow best practices that ensure type safety, maintainability, and developer productivity.

## 1. Use Strict Type Checking

Enable strict mode in your `tsconfig.json` for maximum type safety:

```json
{
  "compilerOptions": {
    "strict": true,
    "noImplicitAny": true,
    "strictNullChecks": true,
    "strictFunctionTypes": true,
    "strictBindCallApply": true,
    "strictPropertyInitialization": true,
    "noImplicitReturns": true,
    "noFallthroughCasesInSwitch": true,
    "noUncheckedIndexedAccess": true
  }
}
```

## 2. Prefer Interfaces Over Types (Mostly)

Use interfaces for object shapes and classes:

```typescript
// ✅ Good - Interface for object shapes
interface User {
  id: number;
  name: string;
  email: string;
  isActive: boolean;
}

// ✅ Good - Interface extending other interfaces
interface AdminUser extends User {
  permissions: string[];
  role: 'admin' | 'super-admin';
}

// ✅ Good - Type for unions and primitives
type Status = 'loading' | 'success' | 'error';
type ID = string | number;
```

## 3. Use Union Types for Better Type Safety

```typescript
// ✅ Good - Union types for better type safety
type ButtonVariant = 'primary' | 'secondary' | 'danger';
type ButtonSize = 'small' | 'medium' | 'large';

interface ButtonProps {
  variant: ButtonVariant;
  size: ButtonSize;
  children: React.ReactNode;
  onClick?: () => void;
}

// ❌ Avoid - Using string for everything
interface BadButtonProps {
  variant: string; // Too broad
  size: string;    // Too broad
}
```

## 4. Leverage Generic Types

```typescript
// ✅ Good - Generic function
function createArray<T>(length: number, value: T): T[] {
  return Array(length).fill(value);
}

// ✅ Good - Generic interface
interface ApiResponse<T> {
  data: T;
  status: number;
  message: string;
}

// ✅ Good - Generic class
class DataStore<T> {
  private data: T[] = [];

  add(item: T): void {
    this.data.push(item);
  }

  get(index: number): T | undefined {
    return this.data[index];
  }

  getAll(): T[] {
    return [...this.data];
  }
}
```

## 5. Use Type Guards for Runtime Type Checking

```typescript
// ✅ Good - Type guard functions
function isString(value: unknown): value is string {
  return typeof value === 'string';
}

function isUser(obj: unknown): obj is User {
  return (
    typeof obj === 'object' &&
    obj !== null &&
    'id' in obj &&
    'name' in obj &&
    'email' in obj
  );
}

// Usage
function processValue(value: unknown): void {
  if (isString(value)) {
    console.log(value.toUpperCase()); // TypeScript knows value is string
  }
  
  if (isUser(value)) {
    console.log(value.name); // TypeScript knows value is User
  }
}
```

## 6. Prefer Const Assertions

```typescript
// ✅ Good - Const assertion
const COLORS = ['red', 'green', 'blue'] as const;
type Color = typeof COLORS[number]; // 'red' | 'green' | 'blue'

// ✅ Good - Const assertion for objects
const CONFIG = {
  apiUrl: 'https://api.example.com',
  timeout: 5000,
  retries: 3,
} as const;

// ❌ Avoid - Mutable arrays/objects
const badColors = ['red', 'green', 'blue']; // string[]
```

## 7. Use Optional Properties Wisely

```typescript
// ✅ Good - Optional properties with defaults
interface FormConfig {
  required?: boolean;
  minLength?: number;
  maxLength?: number;
  pattern?: RegExp;
}

function validateField(value: string, config: FormConfig = {}): boolean {
  const {
    required = false,
    minLength = 0,
    maxLength = Infinity,
    pattern
  } = config;

  if (required && !value) return false;
  if (value.length < minLength) return false;
  if (value.length > maxLength) return false;
  if (pattern && !pattern.test(value)) return false;

  return true;
}
```

## 8. Use Discriminated Unions

```typescript
// ✅ Good - Discriminated union
type RequestState = 
  | { status: 'idle' }
  | { status: 'loading' }
  | { status: 'success'; data: User[] }
  | { status: 'error'; error: string };

function handleRequest(state: RequestState): void {
  switch (state.status) {
    case 'idle':
      console.log('Ready to make request');
      break;
    case 'loading':
      console.log('Request in progress...');
      break;
    case 'success':
      console.log(`Loaded ${state.data.length} users`);
      break;
    case 'error':
      console.error(`Error: ${state.error}`);
      break;
  }
}
```

## 9. Use Utility Types

```typescript
// ✅ Good - Utility types
interface User {
  id: number;
  name: string;
  email: string;
  password: string;
  createdAt: Date;
  updatedAt: Date;
}

// Create a type for API responses (without password)
type UserResponse = Omit<User, 'password'>;

// Create a type for user creation (without auto-generated fields)
type CreateUser = Omit<User, 'id' | 'createdAt' | 'updatedAt'>;

// Make all properties optional
type PartialUser = Partial<User>;

// Make all properties required
type RequiredUser = Required<User>;

// Pick specific properties
type UserBasicInfo = Pick<User, 'id' | 'name' | 'email'>;

// Record type for mapping
type UserRoles = Record<string, 'admin' | 'user' | 'guest'>;
```

## 10. Use Function Overloads Sparingly

```typescript
// ✅ Good - Function overloads for complex APIs
function formatDate(date: Date): string;
function formatDate(date: string): string;
function formatDate(date: Date | string): string {
  const dateObj = typeof date === 'string' ? new Date(date) : date;
  return dateObj.toISOString();
}

// ✅ Good - Method overloads
class Calculator {
  add(a: number, b: number): number;
  add(a: string, b: string): string;
  add(a: number | string, b: number | string): number | string {
    if (typeof a === 'number' && typeof b === 'number') {
      return a + b;
    }
    return `${a}${b}`;
  }
}
```

## 11. Use Enums for Constants

```typescript
// ✅ Good - String enums
enum UserRole {
  ADMIN = 'admin',
  USER = 'user',
  GUEST = 'guest'
}

// ✅ Good - Numeric enums with explicit values
enum HttpStatus {
  OK = 200,
  CREATED = 201,
  BAD_REQUEST = 400,
  UNAUTHORIZED = 401,
  NOT_FOUND = 404,
  INTERNAL_SERVER_ERROR = 500
}

// ✅ Good - Const enums for better performance
const enum Direction {
  UP = 'up',
  DOWN = 'down',
  LEFT = 'left',
  RIGHT = 'right'
}
```

## 12. Use Namespaces for Organization

```typescript
// ✅ Good - Namespace for related functionality
namespace Validation {
  export interface Rule {
    test: (value: any) => boolean;
    message: string;
  }

  export function isEmail(value: string): boolean {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
  }

  export function isRequired(value: any): boolean {
    return value !== null && value !== undefined && value !== '';
  }
}

// Usage
const emailRule: Validation.Rule = {
  test: Validation.isEmail,
  message: 'Invalid email format'
};
```

## 13. Use Module Augmentation

```typescript
// ✅ Good - Extending existing modules
declare module 'express' {
  interface Request {
    user?: User;
  }
}

// ✅ Good - Extending global types
declare global {
  interface Window {
    analytics: {
      track(event: string, data?: any): void;
    };
  }
}
```

## 14. Use Conditional Types

```typescript
// ✅ Good - Conditional types
type NonNullable<T> = T extends null | undefined ? never : T;

type ReturnType<T> = T extends (...args: any[]) => infer R ? R : any;

// ✅ Good - Mapped types with conditionals
type EventMap = {
  click: MouseEvent;
  keydown: KeyboardEvent;
  submit: SubmitEvent;
};

type EventHandler<T extends keyof EventMap> = (event: EventMap[T]) => void;

// ✅ Good - Template literal types
type HttpMethod = 'GET' | 'POST' | 'PUT' | 'DELETE';
type ApiEndpoint = `/api/${string}`;
type FullUrl = `${HttpMethod} ${ApiEndpoint}`;
```

## 15. Use Assertion Functions

```typescript
// ✅ Good - Assertion functions
function assertIsString(value: unknown): asserts value is string {
  if (typeof value !== 'string') {
    throw new Error('Expected string');
  }
}

function assertIsUser(value: unknown): asserts value is User {
  if (!isUser(value)) {
    throw new Error('Expected User object');
  }
}

// Usage
function processUserData(data: unknown): void {
  assertIsUser(data);
  // TypeScript knows data is User here
  console.log(data.name);
}
```

## Best Practices Summary

1. **Enable strict mode** for maximum type safety
2. **Use interfaces for objects**, types for unions/primitives
3. **Leverage union types** for better type safety
4. **Use generics** for reusable, type-safe code
5. **Implement type guards** for runtime type checking
6. **Use const assertions** for immutable values
7. **Prefer optional properties** with defaults
8. **Use discriminated unions** for complex state management
9. **Leverage utility types** for type transformations
10. **Use enums** for constants and better IntelliSense

## Conclusion

TypeScript best practices help you write more maintainable, type-safe, and robust applications. By following these guidelines, you'll create better code that's easier to refactor, debug, and extend.

### Key Takeaways

- **Strict mode is your friend** - enable it for better type safety
- **Interfaces and types have different use cases** - choose wisely
- **Utility types save time** - learn and use them effectively
- **Type guards improve runtime safety** - implement them for complex types
- **Generics make code reusable** - use them for flexible, type-safe functions

Happy coding with TypeScript! 🚀 