#!/usr/bin/env python3
"""
Cooking AI Agent - Interactive console application for recipe search and ingredient extraction.
Uses Microsoft Agent Framework with GitHub-hosted models.
"""

import os
import json
import asyncio
from typing import Any
from dotenv import load_dotenv

from agent_framework.openai import OpenAIChatClient
from agent_framework.agents import AgentConfiguration, AssistantAgent

# Load environment variables
load_dotenv()

# Sample recipe database
RECIPES_DATABASE = {
    "паста болоньезе": {
        "ingredients": ["макароны", "говяжий фарш", "томаты", "лук", "чеснок", "оливковое масло", "пармезан"],
        "instructions": "1. Обжарить лук и чеснок. 2. Добавить фарш. 3. Добавить томаты. 4. Варить макароны. 5. Подать с сыром.",
        "time": "30 минут"
    },
    "куриный суп": {
        "ingredients": ["курица", "морковь", "лук", "сельдерей", "картофель", "лапша", "соль", "перец"],
        "instructions": "1. Вскипятить воду. 2. Добавить курицу. 3. Добавить овощи. 4. Варить 30 минут. 5. Добавить лапшу.",
        "time": "45 минут"
    },
    "цезарь салат": {
        "ingredients": ["романо салат", "пармезан", "гренки", "яйцо", "анчоусы", "лимон", "оливковое масло"],
        "instructions": "1. Нарезать салат. 2. Добавить гренки. 3. Посыпать сыром. 4. Полить соусом. 5. Перемешать.",
        "time": "15 минут"
    },
    "омлет": {
        "ingredients": ["яйца", "молоко", "масло сливочное", "соль", "перец", "зелень"],
        "instructions": "1. Взбить яйца с молоком. 2. Нагреть масло. 3. Вылить смесь. 4. Готовить 5-7 минут. 5. Сложить пополам.",
        "time": "10 минут"
    },
    "борщ": {
        "ingredients": ["свекла", "капуста", "говядина", "картофель", "помидоры", "лук", "чеснок", "сметана"],
        "instructions": "1. Отварить говядину. 2. Нарезать и обжарить овощи. 3. Добавить в бульон. 4. Варить 40 минут. 5. Подать со сметаной.",
        "time": "90 минут"
    }
}

# Common ingredients and their properties
INGREDIENTS_KNOWLEDGE = {
    "помидоры": {"тип": "овощ", "калории": 18, "витамины": ["C", "K"]},
    "курица": {"тип": "мясо", "калории": 165, "витамины": ["B6", "B12"]},
    "макароны": {"тип": "крупа", "калории": 131, "витамины": ["B1", "B2"]},
    "молоко": {"тип": "молочный продукт", "калории": 61, "витамины": ["A", "D"]},
    "яйца": {"тип": "мясное", "калории": 155, "витамины": ["B12", "D"]},
    "рис": {"тип": "крупа", "калории": 130, "витамины": ["B1", "B3"]},
    "морковь": {"тип": "овощ", "калории": 41, "витамины": ["A", "K"]},
    "лук": {"тип": "овощ", "калории": 40, "витамины": ["C", "B6"]},
    "чеснок": {"тип": "овощ", "калории": 149, "витамины": ["C", "B6"]},
    "оливковое масло": {"тип": "масло", "калории": 884, "витамины": ["E", "K"]}
}


def search_recipes(query: str) -> dict:
    """
    Search for recipes by name or ingredient.

    Args:
        query: Search query (recipe name or ingredient)

    Returns:
        Dictionary with found recipes
    """
    query_lower = query.lower()
    results = {}

    # Search by recipe name
    for recipe_name, recipe_data in RECIPES_DATABASE.items():
        if query_lower in recipe_name:
            results[recipe_name] = recipe_data

    # Search by ingredient
    if not results:
        for recipe_name, recipe_data in RECIPES_DATABASE.items():
            if any(query_lower in ingredient for ingredient in recipe_data["ingredients"]):
                results[recipe_name] = recipe_data

    return results if results else {"error": f"Рецепты с '{query}' не найдены"}


def extract_ingredients(recipe_name: str) -> dict:
    """
    Extract and analyze ingredients from a recipe.

    Args:
        recipe_name: Name of the recipe

    Returns:
        Dictionary with ingredient information
    """
    recipe_name_lower = recipe_name.lower()

    # Find the recipe
    for name, data in RECIPES_DATABASE.items():
        if recipe_name_lower in name:
            ingredients_info = {}
            for ingredient in data["ingredients"]:
                ingredient_lower = ingredient.lower()
                if ingredient_lower in INGREDIENTS_KNOWLEDGE:
                    ingredients_info[ingredient] = INGREDIENTS_KNOWLEDGE[ingredient_lower]
                else:
                    ingredients_info[ingredient] = {"информация": "основной ингредиент"}

            return {
                "рецепт": name,
                "ингредиенты": ingredients_info,
                "количество": len(data["ingredients"])
            }

    return {"error": f"Рецепт '{recipe_name}' не найден"}


def get_recipe_details(recipe_name: str) -> dict:
    """
    Get full details of a recipe.

    Args:
        recipe_name: Name of the recipe

    Returns:
        Dictionary with full recipe information
    """
    recipe_name_lower = recipe_name.lower()

    for name, data in RECIPES_DATABASE.items():
        if recipe_name_lower in name:
            return {
                "название": name,
                "ингредиенты": data["ingredients"],
                "инструкции": data["instructions"],
                "время_приготовления": data["time"]
            }

    return {"error": f"Рецепт '{recipe_name}' не найден"}


async def main():
    """Main function to run the cooking AI agent."""

    # Get API key from environment
    github_token = os.getenv("GITHUB_TOKEN")
    if not github_token:
        print("❌ GITHUB_TOKEN не найден в переменных окружения")
        print("⚠️  Установите GITHUB_TOKEN для использования GitHub Models API")
        return

    # Initialize the chat client
    client = OpenAIChatClient(
        model="gpt-4o-mini",  # GitHub model
        base_url="https://models.inference.ai.azure.com",
        api_key=github_token
    )

    # Define agent tools
    tools = [
        {
            "type": "function",
            "function": {
                "name": "search_recipes",
                "description": "Поиск рецептов по названию или ингредиенту",
                "parameters": {
                    "type": "object",
                    "properties": {
                        "query": {
                            "type": "string",
                            "description": "Поисковый запрос (название рецепта или ингредиент)"
                        }
                    },
                    "required": ["query"]
                }
            }
        },
        {
            "type": "function",
            "function": {
                "name": "extract_ingredients",
                "description": "Извлечение и анализ ингредиентов из рецепта",
                "parameters": {
                    "type": "object",
                    "properties": {
                        "recipe_name": {
                            "type": "string",
                            "description": "Название рецепта"
                        }
                    },
                    "required": ["recipe_name"]
                }
            }
        },
        {
            "type": "function",
            "function": {
                "name": "get_recipe_details",
                "description": "Получить полную информацию о рецепте",
                "parameters": {
                    "type": "object",
                    "properties": {
                        "recipe_name": {
                            "type": "string",
                            "description": "Название рецепта"
                        }
                    },
                    "required": ["recipe_name"]
                }
            }
        }
    ]

    # Initialize agent configuration
    agent_config = AgentConfiguration(
        name="Cooking Chef",
        description="AI агент для помощи с поиском рецептов и анализом ингредиентов",
        model="gpt-4o-mini",
        instructions="""Вы - опытный кулинарный AI агент. Ваша задача:
1. Помогать пользователям искать рецепты по названию или ингредиентам
2. Анализировать и объяснять ингредиенты рецептов
3. Предоставлять подробные инструкции приготовления
4. Давать советы по приготовлению и заменам ингредиентов

Всегда вежливы и помощны. Используйте доступные инструменты для поиска и анализа рецептов."""
    )

    # Create agent
    agent = AssistantAgent(
        name="Cooking Chef",
        client=client,
        tools=tools
    )

    print("🍳 Добро пожаловать в Cooking AI Agent!")
    print("=" * 50)
    print("Я помогу вам найти рецепты и проанализировать ингредиенты")
    print("Введите 'выход' для завершения")
    print("=" * 50)

    # Conversation history
    conversation_history = []

    # Main interaction loop
    while True:
        try:
            user_input = input("\n👤 Вы: ").strip()

            if user_input.lower() in ["выход", "exit", "quit"]:
                print("👨‍🍳 Спасибо за использование Cooking AI Agent! До свидания! 👋")
                break

            if not user_input:
                continue

            # Add user message to history
            conversation_history.append({
                "role": "user",
                "content": user_input
            })

            # Process with agent
            print("\n⏳ Обработка запроса...")

            # Get response from client with tools
            response = await client.complete(
                messages=conversation_history,
                tools=tools,
                temperature=0.7,
                max_tokens=500
            )

            assistant_message = response.choices[0].message

            # Handle tool calls
            if hasattr(assistant_message, 'tool_calls') and assistant_message.tool_calls:
                for tool_call in assistant_message.tool_calls:
                    tool_name = tool_call.function.name
                    tool_args = json.loads(tool_call.function.arguments)

                    # Execute appropriate tool
                    if tool_name == "search_recipes":
                        tool_result = search_recipes(tool_args.get("query", ""))
                    elif tool_name == "extract_ingredients":
                        tool_result = extract_ingredients(tool_args.get("recipe_name", ""))
                    elif tool_name == "get_recipe_details":
                        tool_result = get_recipe_details(tool_args.get("recipe_name", ""))
                    else:
                        tool_result = {"error": "Неизвестный инструмент"}

                    print(f"\n🔧 Инструмент: {tool_name}")
                    print(f"📊 Результат: {json.dumps(tool_result, ensure_ascii=False, indent=2)}")

                    # Add tool result to conversation
                    conversation_history.append({
                        "role": "assistant",
                        "content": f"Tool called: {tool_name} with result: {tool_result}"
                    })

            # Add assistant response to history
            if hasattr(assistant_message, 'content') and assistant_message.content:
                conversation_history.append({
                    "role": "assistant",
                    "content": assistant_message.content
                })
                print(f"\n👨‍🍳 Агент: {assistant_message.content}")

        except KeyboardInterrupt:
            print("\n\n👨‍🍳 До свидания! 👋")
            break
        except Exception as e:
            print(f"❌ Ошибка: {str(e)}")
            print("Попробуйте еще раз...")


if __name__ == "__main__":
    asyncio.run(main())
