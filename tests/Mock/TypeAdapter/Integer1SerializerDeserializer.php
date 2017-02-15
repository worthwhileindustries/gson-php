<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Test\Mock\TypeAdapter;

use DateTime;
use Tebru\Collection\ListInterface;
use Tebru\Collection\MapInterface;
use Tebru\Gson\Element\JsonArray;
use Tebru\Gson\Element\JsonElement;
use Tebru\Gson\Element\JsonObject;
use Tebru\Gson\Internal\PhpType;
use Tebru\Gson\JsonDeserializationContext;
use Tebru\Gson\JsonDeserializer;
use Tebru\Gson\JsonSerializationContext;
use Tebru\Gson\JsonSerializer;
use Tebru\Gson\Test\Mock\GsonMock;
use Tebru\Gson\Test\Mock\GsonObjectMock;

/**
 * Class Integer1SerializerDeserializer
 *
 * @author Nate Brunette <n@tebru.net>
 */
class Integer1SerializerDeserializer implements JsonDeserializer, JsonSerializer
{
    /**
     * Called during deserialization process, passing in the JsonElement for the type.  Use
     * the JsonDeserializationContext if you want to delegate deserialization of sub types.
     *
     * @param JsonElement $jsonElement
     * @param PhpType $type
     * @param JsonDeserializationContext $context
     * @return mixed
     */
    public function deserialize(JsonElement $jsonElement, PhpType $type, JsonDeserializationContext $context): GsonMock
    {
        $json = $jsonElement->asJsonObject();

        $mock = new GsonMock();
        $mock->setInteger($json->getAsInteger('integer') + 1);
        $mock->setFloat($json->getAsFloat('float'));
        $mock->setString($json->getAsString('string'));
        $mock->setBoolean($json->getAsBoolean('boolean'));
        $mock->setArray($json->getAsArray('array'));
        $mock->setArrayList($context->deserialize($json->get('array_list'), ListInterface::class));
        $mock->setHashMap($context->deserialize($json->get('hash_map'), MapInterface::class));
        $mock->setDate($context->deserialize($json->get('date'), DateTime::class));
        $mock->public = $json->getAsString('public');
        $mock->setSince($json->getAsString('since'));
        $mock->setUntil($json->getAsString('until'));
        $mock->setMyAccessor($json->getAsString('accessor'));
        $mock->setSerializedname($json->getAsString('serialized_name'));

        $type = $json->getAsArray('type');
        $type = array_map(function (int $value) { return $value + 1; }, $type);
        $mock->setType($type);

        $mock->setJsonAdapter(new GsonObjectMock($json->getAsString('json_adapter')));
        $mock->setExpose($json->getAsBoolean('expose'));
        $mock->setExcludeFromStrategy($json->getAsBoolean('exclude_from_strategy'));
        $mock->setGsonObjectMock($context->deserialize($json->get('gson_object_mock'), GsonObjectMock::class));

        return $mock;
    }

    /**
     * Called during serialization process, passing in the object and type that should
     * be serialized.  Delegate serialization using the provided context.  Method should
     * return a JsonElement.
     *
     * @param GsonMock $object
     * @param PhpType $type
     * @param JsonSerializationContext $context
     * @return JsonElement
     */
    public function serialize($object, PhpType $type, JsonSerializationContext $context): JsonElement
    {
        $jsonObject = new JsonObject();
        $jsonObject->addInteger('integer', $object->getInteger() + 1);
        $jsonObject->addFloat('float', $object->getFloat());
        $jsonObject->addString('string', $object->getString());
        $jsonObject->addBoolean('string', $object->getBoolean());
        $jsonObject->add('array', $context->serialize($object->getArray()));
        $jsonObject->add('array_list', $context->serialize($object->getArrayList()));
        $jsonObject->add('hash_map', $context->serialize($object->getHashMap()));
        $jsonObject->add('date', $context->serialize($object->getDate()));
        $jsonObject->add('public', $object->public);
        $jsonObject->addString('since', $object->getSince());
        $jsonObject->addString('until', $object->getUntil());
        $jsonObject->addString('accessor', $object->getMyAccessor());
        $jsonObject->addString('serialized_name', $object->getSerializedname());

        $jsonArray = new JsonArray();
        foreach ($object->getType() as $item) {
            $jsonArray->addInteger($item + 1);
        }
        $jsonObject->add('type', $jsonArray);

        $jsonAdapter = new JsonObject();
        $jsonAdapter->add('foo', $object->getJsonAdapter()->getFoo());
        $jsonObject->add('json_adapter', $jsonAdapter);

        $jsonObject->addBoolean('expose', $object->getExpose());
        $jsonObject->addBoolean('exclude_from_strategy', $object->getExcludeFromStrategy());
        $jsonObject->add('gson_object_mock', $context->serialize($object->getGsonObjectMock()));
    }
}
